<template>
  <form 
    @submit.prevent="submit"
    class="w-full h-full flex flex-col justify-between"
    v-if="!isLoading">
    <div class="flex flex-col gap-y-20">
      <InputGroup>
        <InputLabel label="Kartei" id="archive_selection" />
          <InputSelectButtons
              id="archive_selection"
              v-model="selectedArchiveId"
              :multiple="false"
              name="archive_selection"
              wrapperClasses="flex flex-col gap-y-8"
              :options="archiveOptions"
              @update:modelValue="selectArchive"
            >
              <template #icon="{ option }">
                <IconCheckmark v-if="isSaved(option.value)" />
              </template>
            </InputSelectButtons>
      </InputGroup>
      <div :class="{'opacity-20 select-none pointer-events-none': !selectedArchiveId}" class="flex flex-col gap-y-20">
        <template v-if="!isSaved(selectedArchiveId)">
          <InputGroup>
            <InputLabel label="Rechte" id="role" />
            <InputSelect
              id="role"
              v-model="currentRole"
              :options="roles"
              :error="errors.role"
              @update:modelValue="updateRole"
            />
          </InputGroup>
          <InputGroup v-if="permissionsArchive.length > 0">
            <InputLabel label="Kartei" id="archive" />
            <InputSelectButtons
              id="archive"
              v-model="currentPermissions"
              :multiple="true"
              name="permissions"
              wrapperClasses="grid grid-cols-2 gap-8"
              :options="permissionsArchive" />
          </InputGroup>
          <InputGroup v-if="permissionsArchiveEdit.length > 0">
            <InputLabel label="Bearbeiten" id="archive_edit" />
            <InputSelectButtons
              id="archive_edit"
              v-model="currentPermissions"
              :multiple="true"
              name="permissions"
              wrapperClasses="grid grid-cols-2 gap-8"
              :options="permissionsArchiveEdit" />
          </InputGroup>
          <InputGroup v-if="permissionsCard.length > 0">
            <InputLabel label="Karten" id="card" />
            <InputSelectButtons
              id="card"
              v-model="currentPermissions"
              :multiple="true"
              name="permissions"
              wrapperClasses="grid grid-cols-2 gap-8"
              :options="permissionsCard" />
          </InputGroup>
          <InputGroup v-if="permissionsCardEdit.length > 0">
            <InputLabel label="Bearbeiten" id="card_edit" />
            <InputSelectButtons
              id="card_edit"
              v-model="currentPermissions"
              :multiple="true"
              name="permissions"
              wrapperClasses="grid grid-cols-2 gap-8"
              :options="permissionsCardEdit" />
          </InputGroup>
        </template>
        <template v-else>
          <div v-if="!invitationSent[selectedArchiveId]">
            <ButtonAuth label="Einladungslink versenden" @click="send" :disabled="isSending" />
            <p class="text-sm p-8 mt-8">Mit Abschicken des Einladungslinks erhält {{ user.firstname }} {{ user.name }} Zugang zu den ausgewählten Karteien. Zugriffsrechte können innerhalb der Kartei in den Voreinstellungen angepasst werden.</p>
          </div>
          <div v-else>
            <template v-if="hasUnhandledArchives">
              <p class="text-sm p-8 mt-8">
                Einladungslink wurde versendet. Sie können Berechtigungen für weitere Karteien setzen oder den Prozess abschliessen.
              </p>
            </template>
            <template v-else>
              <p class="text-sm p-8 mt-8">
                Einladungslink wurde versendet.
              </p>
            </template>
          </div>
        </template>
      </div>
    </div>
    <ButtonGroup>
      <ButtonPrimary 
        v-if="!isSaved(selectedArchiveId)" 
        type="submit" 
        label="Speichern" 
        :disabled="isSaving || !selectedArchiveId || !hasChanges" 
        :loading="isSaving" />
      <template v-else>
        <ButtonPrimary 
          @click="$emit('success')" 
          label="Abschliessen" 
          :disabled="isSending" />
      </template>
    </ButtonGroup>
  </form>
  <div v-else class="flex justify-center items-center h-full">
    <span class="text-gray-500">Laden...</span>
  </div>
</template>

<script setup>
import { ref, computed, provide, watch, onMounted } from 'vue';
import { useToastStore } from '@/components/toast/stores/toast';
import { useDialogStore } from '@/components/dialog/stores/dialog';
import { getRoles, getRolesWithPermissions } from '@/services/api/role';
import { getPermissions } from '@/services/api/permission';
import { storePermissions, sendInvitation } from '@/services/api/user';
import { getArchivesByAdmin } from '@/services/api/archive';
import InputGroup from '@/components/forms/Group.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputSelect from '@/components/forms/Select.vue';
import InputSelectButtons from '@/components/forms/SelectButtons.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import ButtonAuth from '@/components/buttons/Auth.vue';
import IconCheckmark from '@/components/icons/Checkmark.vue';

const toast = useToastStore();
const dialogStore = useDialogStore();
const DEFAULT_ROLE = 5; // Manager role

// Props
const props = defineProps({
  user: {
    type: Object,
    required: true
  },
  existingPermissions: {
    type: Array,
    default: () => []
  }
});

// Emits
const emit = defineEmits(['finish', 'cancel', 'success']);

// Core state
const isLoading = ref(true);
const isSaving = ref(false);
const isSending = ref(false);
const selectedArchiveId = ref('');
const currentArchiveId = ref(null); // Track the archive currently being edited
const archives = ref([]);
const roles = ref([]);
const permissions = ref({});
const permissionsByRole = ref({});
const invitationSent = ref({});

// Permission categories
const permissionsArchive = ref([]);
const permissionsArchiveEdit = ref([]);
const permissionsCard = ref([]);
const permissionsCardEdit = ref([]);

// Error handling
const errors = ref({
  role: null
});

// Structured state for permissions
const archivePermissions = ref({});

// Computed properties
const archiveOptions = computed(() => {
  return archives.value.map(archive => ({
    value: archive.uuid,
    label: archive.title,
    disabled: isSaved(archive.uuid)
  }));
});

const currentRole = computed({
  get: () => {
    if (!selectedArchiveId.value) return DEFAULT_ROLE;
    return archivePermissions.value[selectedArchiveId.value]?.role || DEFAULT_ROLE;
  },
  set: (value) => {
    if (!selectedArchiveId.value) return;
    
    if (!archivePermissions.value[selectedArchiveId.value]) {
      archivePermissions.value[selectedArchiveId.value] = {
        role: value,
        selectedPermissions: [],
        isDirty: true,
        isSaved: false,
        originalRole: DEFAULT_ROLE,
        originalPermissions: []
      };
    } else {
      archivePermissions.value[selectedArchiveId.value].role = value;
      archivePermissions.value[selectedArchiveId.value].isDirty = true;
    }
  }
});

const currentPermissions = computed({
  get: () => {
    if (!selectedArchiveId.value) return [];
    return archivePermissions.value[selectedArchiveId.value]?.selectedPermissions || [];
  },
  set: (value) => {
    if (!selectedArchiveId.value) return;
    
    if (!archivePermissions.value[selectedArchiveId.value]) {
      archivePermissions.value[selectedArchiveId.value] = {
        role: DEFAULT_ROLE,
        selectedPermissions: value,
        isDirty: true,
        isSaved: false,
        originalRole: DEFAULT_ROLE,
        originalPermissions: []
      };
    } else {
      archivePermissions.value[selectedArchiveId.value].selectedPermissions = value;
      archivePermissions.value[selectedArchiveId.value].isDirty = true;
    }
  }
});

const hasChanges = computed(() => {
  if (!selectedArchiveId.value) return false;
  
  const archiveData = archivePermissions.value[selectedArchiveId.value];
  if (!archiveData) return false;
  
  // First check the isDirty flag - this is set when we first load or change permissions
  if (archiveData.isDirty) return true;
  
  // Check if role changed
  if (archiveData.role !== archiveData.originalRole) return true;
  
  // Check if permissions changed
  const original = [...archiveData.originalPermissions].sort();
  const current = [...archiveData.selectedPermissions].sort();
  
  if (original.length !== current.length) return true;
  return !original.every((val, idx) => val === current[idx]);
});

const hasUnhandledArchives = computed(() => {
  return archives.value.some(archive => !isSaved(archive.uuid));
});

// Methods
function isSaved(archiveId) {
  if (!archiveId) return false;
  return archivePermissions.value[archiveId]?.isSaved || false;
}

async function selectArchive(archiveId) {
  // Check if selecting a different archive than the current one being edited
  if (archiveId !== currentArchiveId.value) {
    // Check if there are unsaved changes in the current archive
    let hasUnsavedChanges = false;
    
    if (currentArchiveId.value) {
      const archiveData = archivePermissions.value[currentArchiveId.value];
      if (archiveData) {
        // Check if dirty flag is set
        if (archiveData.isDirty) {
          hasUnsavedChanges = true;
        }
        // Check if role changed
        else if (archiveData.role !== archiveData.originalRole) {
          hasUnsavedChanges = true;
        }
        // Check if permissions changed
        else {
          const original = [...archiveData.originalPermissions].sort();
          const current = [...archiveData.selectedPermissions].sort();
          
          if (original.length !== current.length) {
            hasUnsavedChanges = true;
          } else {
            hasUnsavedChanges = !original.every((val, idx) => val === current[idx]);
          }
        }
      }
    }
    
    if (currentArchiveId.value && hasUnsavedChanges) {
      const shouldDiscard = await confirmDiscardChanges();
      
      if (!shouldDiscard) {
        // User clicked "Abbrechen" (Cancel), revert selection
        selectedArchiveId.value = currentArchiveId.value;
        return;
      }
      // User clicked "Verwerfen", continue with new selection
    }
    
    // Set the new selected archive
    selectedArchiveId.value = archiveId;
    currentArchiveId.value = archiveId;
    
    // Initialize permissions if needed
    if (archiveId) {
      // Check if this archive already has permissions
      const isNewArchive = !archivePermissions.value[archiveId] || 
                          (archivePermissions.value[archiveId] && 
                           archivePermissions.value[archiveId].selectedPermissions.length === 0);
      
      if (isNewArchive) {
        // Initialize with default structure if it doesn't exist
        if (!archivePermissions.value[archiveId]) {
          archivePermissions.value[archiveId] = {
            role: DEFAULT_ROLE,
            selectedPermissions: [],
            isDirty: true, // Mark as dirty immediately when creating
            isSaved: false,
            originalRole: DEFAULT_ROLE,
            originalPermissions: []
          };
        } else {
          // If it exists but has no permissions, mark it as dirty
          archivePermissions.value[archiveId].isDirty = true;
        }
        
        // Set default permissions based on role
        // Wait for permissionsByRole to be populated
        if (Object.keys(permissionsByRole.value).length > 0) {
          setDefaultPermissionsForRole(DEFAULT_ROLE, archiveId);
        } else {
          // If permissions aren't loaded yet, set up a watcher to apply them when ready
          const unwatch = watch(permissionsByRole, () => {
            if (Object.keys(permissionsByRole.value).length > 0) {
              setDefaultPermissionsForRole(DEFAULT_ROLE, archiveId);
              unwatch(); // Remove the watcher once permissions are set
            }
          }, { immediate: true });
        }
      } else {
        // Load existing permissions
        currentRole.value = archivePermissions.value[archiveId].role;
        // Mark as dirty if user makes any changes
        archivePermissions.value[archiveId].isDirty = true;
      }
    }
  }
}

function updateRole(roleId) {
  if (!selectedArchiveId.value) return;
  
  // If roleId is an event object (from @change), extract the value
  if (roleId && typeof roleId === 'object' && roleId.target) {
    roleId = roleId.target.value;
  }
  
  // Convert to number if it's a string
  if (typeof roleId === 'string') {
    roleId = parseInt(roleId, 10);
  }
  
  // Update the role
  currentRole.value = roleId;
  
  // Set default permissions for this role
  setDefaultPermissionsForRole(roleId, selectedArchiveId.value);
}

function setDefaultPermissionsForRole(roleId, archiveId) {
  const role = Object.values(permissionsByRole.value).find(r => r.id == roleId);
  if (!role) {
    return;
  }
  
  // Extract permissions
  const rolePermissions = role.permissions || [];
  const permissionIds = rolePermissions.map(p => p.id);
  
  // Set the selected permissions
  if (archivePermissions.value[archiveId]) {
    archivePermissions.value[archiveId].selectedPermissions = permissionIds;
    archivePermissions.value[archiveId].isDirty = true;
  }
}

async function confirmDiscardChanges() {
  return new Promise((resolve) => {
    dialogStore.show({
      title: 'Ungespeicherte Änderungen',
      message: 'Du hast ungespeicherte Änderungen. Möchtest du diese verwerfen?',
      confirmLabel: 'Verwerfen',
      cancelLabel: 'Abbrechen',
      size: 'small',
      onConfirm: () => {
        resolve(true);
      },
      onCancel: () => {
        resolve(false);
      }
    });
  });
}

async function submit() {
  try {
    if (!validateForm()) return;
    
    isSaving.value = true;
    
    // Prepare data for API
    const data = {
      archive: selectedArchiveId.value,
      role: currentRole.value,
      permissions: currentPermissions.value
    };

    // Call API to store permissions
    await storePermissions(props.user, data);

    if (archivePermissions.value[selectedArchiveId.value]) {
      archivePermissions.value[selectedArchiveId.value].isSaved = true;
      archivePermissions.value[selectedArchiveId.value].isDirty = false;
      archivePermissions.value[selectedArchiveId.value].originalRole = currentRole.value;
      archivePermissions.value[selectedArchiveId.value].originalPermissions = [...currentPermissions.value];
    }
    
    // Reset currentArchiveId after successful save
    currentArchiveId.value = null;
    
    toast.show('Berechtigungen wurden gespeichert.', 'success');
    emit('success');
  } catch (error) {
    console.error('Failed to save permissions:', error);
    toast.show('Fehler beim Speichern der Berechtigungen.', 'error');
  } finally {
    isSaving.value = false;
  }
}

async function send() {
  if (!selectedArchiveId.value) return;
  
  try {
    isSending.value = true;
    
    // Call API to send invitation
    await sendInvitation(props.user,{ archive: selectedArchiveId.value});
    
    // Mark invitation as sent
    invitationSent.value[selectedArchiveId.value] = true;
    
    toast.show('Einladungslink wurde versendet.', 'success');
  } 
  catch (error) {
    console.error('Failed to send invitation:', error);
    toast.show('Fehler beim Versenden des Einladungslinks.', 'error');
  } 
  finally {
    isSending.value = false;
  }
}

function validateForm() {
  // Reset errors
  errors.value = {
    role: null
  };
  
  // Check if an archive is selected
  if (!selectedArchiveId.value) {
    toast.show('Bitte wähle eine Kartei aus.', 'error');
    return false;
  }
  
  // Check if permissions are selected
  if (currentPermissions.value.length === 0) {
    toast.show('Bitte wähle mindestens eine Berechtigung aus.', 'error');
    return false;
  }
  
  return true;
}

// Initialization
onMounted(async () => {
  try {
    isLoading.value = true;
    await Promise.all([
      fetchArchives(),
      fetchRoles(),
      fetchPermissions(),
      fetchRolesWithPermissions()
    ]);
    
    // Apply existing permissions if provided
    applyExistingPermissions();
  }
  catch (error) {
    console.error('Error initializing component:', error);
    toast.show('Fehler beim Laden der Daten.', 'error');
  } 
  finally {
    isLoading.value = false;
  }
});

async function fetchArchives() {
  try {
    const archivesResponse = await getArchivesByAdmin();
    archives.value = archivesResponse;
  } catch (error) {
    console.error('Failed to fetch archives:', error);
    throw error;
  }
}

function applyExistingPermissions() {
  if (props.existingPermissions && props.existingPermissions.length > 0) {
    props.existingPermissions.forEach(perm => {
      if (perm.archive) {
        archivePermissions.value[perm.archive] = {
          role: perm.role || DEFAULT_ROLE,
          selectedPermissions: perm.selectedPermissions || [],
          isDirty: false,
          isSaved: true,
          originalRole: perm.role || DEFAULT_ROLE,
          originalPermissions: [...(perm.selectedPermissions || [])]
        };
      }
    });
  }
}

async function fetchRoles() {
  try {
    const rolesResponse = await getRoles();
    
    // Ensure roles is always an array
    if (Array.isArray(rolesResponse)) {
      roles.value = rolesResponse;
    } else if (rolesResponse && rolesResponse.data && Array.isArray(rolesResponse.data)) {
      roles.value = rolesResponse.data.map(role => ({
        value: role.id,
        label: role.name
      }));
    } else if (rolesResponse && typeof rolesResponse === 'object') {
      // If it's an object, convert to array
      roles.value = Object.values(rolesResponse).map(role => ({
        value: role.id,
        label: role.name || role.display_name
      }));
    } else {
      console.warn('Unexpected roles response structure:', rolesResponse);
      roles.value = []; // Fallback to empty array
    }
  } catch (error) {
    console.error('Failed to fetch roles:', error);
    roles.value = []; // Ensure it's always an array even on error
    throw error;
  }
}

async function fetchPermissions() {
  try {
    const permissionsResponse = await getPermissions();
    permissions.value = permissionsResponse;
    
    // Initialize all permission arrays as empty
    permissionsArchive.value = [];
    permissionsArchiveEdit.value = [];
    permissionsCard.value = [];
    permissionsCardEdit.value = [];
    
    // Check the structure of the permissions response
    if (permissions.value && permissions.value.archive && Array.isArray(permissions.value.archive)) {
      // If the API returns categorized permissions
      permissionsArchive.value = permissions.value.archive.map(p => ({
        value: p.id,
        label: p.display_name
      }));
      
      if (Array.isArray(permissions.value.archive_edit)) {
        permissionsArchiveEdit.value = permissions.value.archive_edit.map(p => ({
          value: p.id,
          label: p.display_name
        }));
      }
      
      if (Array.isArray(permissions.value.card)) {
        permissionsCard.value = permissions.value.card.map(p => ({
          value: p.id,
          label: p.display_name
        }));
      }
      
      if (Array.isArray(permissions.value.card_edit)) {
        permissionsCardEdit.value = permissions.value.card_edit.map(p => ({
          value: p.id,
          label: p.display_name
        }));
      }
    } else if (Array.isArray(permissions.value)) {
      // If the API returns a flat array of permissions
      permissionsArchive.value = permissions.value
        .filter(p => p.category === 'archive' && !p.name.includes('edit'))
        .map(p => ({
          value: p.id,
          label: p.display_name
        }));
      
      permissionsArchiveEdit.value = permissions.value
        .filter(p => p.category === 'archive' && p.name.includes('edit'))
        .map(p => ({
          value: p.id,
          label: p.display_name
        }));
      
      permissionsCard.value = permissions.value
        .filter(p => p.category === 'card' && !p.name.includes('edit'))
        .map(p => ({
          value: p.id,
          label: p.display_name
        }));
      
      permissionsCardEdit.value = permissions.value
        .filter(p => p.category === 'card' && p.name.includes('edit'))
        .map(p => ({
          value: p.id,
          label: p.display_name
        }));
    } else {
      console.warn('Unexpected permissions response structure:', permissions.value);
    }
  } catch (error) {
    console.error('Failed to fetch permissions:', error);
    throw error;
  }
}

async function fetchRolesWithPermissions() {
  try {
    const permissionsByRoleResponse = await getRolesWithPermissions();
    
    // Ensure permissionsByRole is always an object
    if (permissionsByRoleResponse && typeof permissionsByRoleResponse === 'object') {
      permissionsByRole.value = permissionsByRoleResponse;
    } else {
      console.warn('Unexpected roles with permissions response structure:', permissionsByRoleResponse);
      permissionsByRole.value = {}; // Fallback to empty object
    }
  } catch (error) {
    console.error('Failed to fetch roles with permissions:', error);
    permissionsByRole.value = {}; // Ensure it's always an object even on error
    throw error;
  }
}

// Add a watch on selectedArchiveId to ensure permissions are set
watch(selectedArchiveId, (newArchiveId) => {
  if (newArchiveId && Object.keys(permissionsByRole.value).length > 0) {
    // Check if this archive needs default permissions
    if (!archivePermissions.value[newArchiveId] || 
        (archivePermissions.value[newArchiveId] && 
         archivePermissions.value[newArchiveId].selectedPermissions.length === 0)) {
      
      // Initialize if needed
      if (!archivePermissions.value[newArchiveId]) {
        archivePermissions.value[newArchiveId] = {
          role: DEFAULT_ROLE,
          selectedPermissions: [],
          isDirty: false,
          isSaved: false,
          originalRole: DEFAULT_ROLE,
          originalPermissions: []
        };
      }
      
      // Force the role to be set
      currentRole.value = DEFAULT_ROLE;
      
      // Force default permissions to be set
      const role = Object.values(permissionsByRole.value).find(r => r.id == DEFAULT_ROLE);
      if (role && role.permissions) {
        const permissionIds = role.permissions.map(p => p.id);
        archivePermissions.value[newArchiveId].selectedPermissions = permissionIds;
      } else {
        console.warn('Role not found or has no permissions:', DEFAULT_ROLE);
      }
    }
  }
}, { immediate: true });

// Provide the permissions map to child components if needed
provide('archivePermissions', archivePermissions);
</script>