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
              v-model="selectedArchive"
              :multiple="false"
              name="archive_selection"
              wrapperClasses="flex flex-col gap-y-8"
              :options="archiveOptionsWithDisabled"
              @change="handleArchiveChange"
            >
              <template #icon="{ option }">
                <IconCheckmark v-if="savedArchives.includes(option.value)" />
              </template>
            </InputSelectButtons>
      </InputGroup>

      <div :class="{'opacity-20 select-none pointer-events-none': !selectedArchive}" class="flex flex-col gap-y-20">
        <template v-if="!savedArchives.includes(selectedArchive)">
          <InputGroup>
            <InputLabel label="Rechte" id="role" />
            <InputSelect
              id="role"
              v-model="form.role"
              :options="roles"
              :error="errors.role"
              @change="handleRoleChange"
            />
          </InputGroup>

          <InputGroup v-if="permissionsArchive.length > 0">
            <InputLabel label="Kartei" id="archive" />
            <InputSelectButtons
              id="archive"
              v-model="form.selectedPermissions"
              :multiple="true"
              name="permissions"
              wrapperClasses="grid grid-cols-2 gap-8"
              :options="permissionsArchive" />
          </InputGroup>

          <InputGroup v-if="permissionsArchiveEdit.length > 0">
            <InputLabel label="Bearbeiten" id="archive_edit" />
            <InputSelectButtons
              id="archive_edit"
              v-model="form.selectedPermissions"
              :multiple="true"
              name="permissions"
              wrapperClasses="grid grid-cols-2 gap-8"
              :options="permissionsArchiveEdit" />
          </InputGroup>

          <InputGroup v-if="permissionsCard.length > 0">
            <InputLabel label="Karten" id="card" />
            <InputSelectButtons
              id="card"
              v-model="form.selectedPermissions"
              :multiple="true"
              name="permissions"
              wrapperClasses="grid grid-cols-2 gap-8"
              :options="permissionsCard" />
          </InputGroup>

          <InputGroup v-if="permissionsCardEdit.length > 0">
            <InputLabel label="Bearbeiten" id="card_edit" />
            <InputSelectButtons
              id="card_edit"
              v-model="form.selectedPermissions"
              :multiple="true"
              name="permissions"
              wrapperClasses="grid grid-cols-2 gap-8"
              :options="permissionsCardEdit" />
          </InputGroup>
        </template>
        <template v-else>
          <div v-if="!invitationSent[selectedArchive]">
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
        v-if="!savedArchives.includes(selectedArchive)" 
        type="submit" 
        label="Speichern" 
        :disabled="isSaving || !selectedArchive || !hasChanges" 
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
import { ref, onMounted, computed, provide, watch, inject } from 'vue';
import { useToastStore } from '@/components/toast/stores/toast';
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
const roles = ref([]);
const permissions = ref([]);
const archives = ref([]);
const selectedArchive = ref(0);
const savedArchives = ref([]);
const invitationSent = ref({});

const permissionsArchive = ref([]);
const permissionsArchiveEdit = ref([]);
const permissionsCard = ref([]);
const permissionsCardEdit = ref([]);
const permissionsByRole = ref({});

const DEFAULT_ROLE = 3; // Manager role

// Archive permissions map to track permissions for each archive
const archivePermissionsMap = ref({});
const originalPermissionsMap = ref({});
const hasUnsavedChanges = ref(false);

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

const form = ref({
  role: DEFAULT_ROLE,
  selectedPermissions: []
});

const errors = ref({
  role: null
});

const isLoading = ref(true);
const isSaving = ref(false);
const isSending = ref(false);

const emit = defineEmits(['finish', 'cancel']);

const archiveOptions = computed(() => {
  return archives.value.map(archive => ({
    value: archive.uuid,
    label: archive.title
  }));
});

// Options with disabled state for saved archives
const archiveOptionsWithDisabled = computed(() => {
  return archives.value.map(archive => ({
    value: archive.uuid,
    label: archive.title,
    disabled: savedArchives.value.includes(archive.uuid)
  }));
});

// Check if there are any unhandled archives left
const hasUnhandledArchives = computed(() => {
  return archives.value.some(archive => !savedArchives.value.includes(archive.uuid));
});

// Computed property to check if there are changes
const hasChanges = computed(() => {
  if (!selectedArchive.value) return false;
  
  const original = originalPermissionsMap.value[selectedArchive.value] || {
    role: DEFAULT_ROLE,
    selectedPermissions: []
  };
  
  const current = {
    role: form.value.role,
    selectedPermissions: form.value.selectedPermissions
  };
  
  // Check if role changed
  if (original.role !== current.role) return true;
  
  // Check if permissions changed
  return !arraysEqual(original.selectedPermissions, current.selectedPermissions);
});

// Watch for changes to mark unsaved changes
watch(() => [form.value.role, form.value.selectedPermissions], () => {
  if (selectedArchive.value) {
    hasUnsavedChanges.value = hasChanges.value;
  }
}, { deep: true });

// Provide the permissions map to child components
provide('archivePermissionsMap', archivePermissionsMap);

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

const fetchArchives = async () => {
  try {
    const archivesResponse = await getArchivesByAdmin();
    archives.value = archivesResponse;
    
    // Initialize permissions map for each archive
    archives.value.forEach(archive => {
      archivePermissionsMap.value[archive.uuid] = {
        role: DEFAULT_ROLE,
        selectedPermissions: []
      };
      
      // Initialize original permissions map for tracking changes
      originalPermissionsMap.value[archive.uuid] = {
        role: DEFAULT_ROLE,
        selectedPermissions: []
      };
    });
  } catch (error) {
    console.error('Failed to fetch archives:', error);
    throw error;
  }
};

// Apply existing permissions if available
const applyExistingPermissions = () => {
  if (props.existingPermissions && props.existingPermissions.length > 0) {
    // Track which archives already have permissions
    const archivesWithPermissions = [];
    
    props.existingPermissions.forEach(perm => {
      if (perm.archive && archivePermissionsMap.value[perm.archive]) {
        const permissions = {
          role: perm.role || DEFAULT_ROLE,
          selectedPermissions: perm.selectedPermissions || []
        };
        
        // Set current permissions
        archivePermissionsMap.value[perm.archive] = {...permissions};
        
        // Set original permissions for change tracking
        originalPermissionsMap.value[perm.archive] = {...permissions};
        
        // Mark as saved
        archivesWithPermissions.push(perm.archive);
      }
    });
    
    // Add to saved archives
    savedArchives.value = [...archivesWithPermissions];
  }
};

const confirmDiscardChanges = () => {
  return confirm('Du hast ungespeicherte Änderungen. Möchtest du diese verwerfen?');
};

const handleArchiveChange = (newArchiveId) => {
  // Check if there are unsaved changes
  if (selectedArchive.value && hasUnsavedChanges.value) {
    if (!confirmDiscardChanges()) {
      // User cancelled, revert selection
      selectedArchive.value = selectedArchive.value;
      return;
    }
  }
  
  // Reset unsaved changes flag
  hasUnsavedChanges.value = false;
  
  if (newArchiveId) {
    // Load the permissions for the selected archive
    const archivePermissions = archivePermissionsMap.value[newArchiveId];
    
    if (archivePermissions) {
      form.value.role = archivePermissions.role;
      form.value.selectedPermissions = [...archivePermissions.selectedPermissions];
    } else {
      // Set defaults if no permissions exist
      form.value.role = DEFAULT_ROLE;
      handleRoleChange();
    }
  } else {
    // No archive selected, reset form
    form.value.role = DEFAULT_ROLE;
    form.value.selectedPermissions = [];
  }
};

// Helper function to compare arrays
const arraysEqual = (a, b) => {
  if (!a || !b) return false;
  if (a.length !== b.length) return false;
  const sortedA = [...a].sort();
  const sortedB = [...b].sort();
  return sortedA.every((val, idx) => val === sortedB[idx]);
};

// Save current form state to the permissions map
const saveFormToMap = () => {
  if (!selectedArchive.value) return;
  
  archivePermissionsMap.value[selectedArchive.value] = {
    role: form.value.role,
    selectedPermissions: [...form.value.selectedPermissions]
  };
};

const validateForm = () => {
  // Reset errors
  errors.value = {
    role: null
  };
  
  // Check if an archive is selected
  if (!selectedArchive.value) {
    toast.show('Bitte wähle eine Kartei aus.', 'error');
    return false;
  }
  
  // Check if permissions are selected
  if (form.value.selectedPermissions.length === 0) {
    toast.show('Bitte wähle mindestens eine Berechtigung aus.', 'error');
    return false;
  }
  
  return true;
};

const submit = async () => {
  try {
    // Save current form state to map
    saveFormToMap();
    
    // Validate form
    if (!validateForm()) {
      return;
    }
    
    // If all validations pass, proceed with saving
    isSaving.value = true;
    
    const archiveId = selectedArchive.value;
    const permissions = archivePermissionsMap.value[archiveId];
    
    // Prepare data for submission (just the current archive)
    const permissionsToSubmit = [{
      archive: archiveId,
      role: permissions.role,
      selectedPermissions: permissions.selectedPermissions
    }];
    
    // Submit permissions for the selected archive
    const response = await storePermissions(props.user, { permissions: permissionsToSubmit });
    
    // Mark archive as saved
    if (!savedArchives.value.includes(archiveId)) {
      savedArchives.value.push(archiveId);
    }
    
    // Update original permissions for change tracking
    originalPermissionsMap.value[archiveId] = {
      role: permissions.role,
      selectedPermissions: [...permissions.selectedPermissions]
    };
    
    // Reset unsaved changes flag
    hasUnsavedChanges.value = false;
    
    toast.show('Berechtigungen erfolgreich gespeichert.', 'success');
  }
  catch (error) {
    console.error('Error saving permissions:', error);
    
    if (error.response?.data?.errors?.role) {
      errors.value.role = error.response.data.errors.role[0];
    }
    
    toast.show('Fehler beim Speichern der Berechtigungen.', 'error');
  }
  finally {
    isSaving.value = false;
  }
};

// Method for sending invitation link
const send = async () => {
  if (isSending.value) return;
  
  try {
    isSending.value = true;
    
    // We're now handling one archive at a time
    const archiveId = selectedArchive.value;
    
    if (!archiveId) {
      toast.show('Keine Kartei ausgewählt.', 'error');
      return;
    }
    
    // Send invitation with just the current archive
    await sendInvitation(props.user, [archiveId]);
    
    // Mark this archive as having invitation sent
    invitationSent.value[archiveId] = true;
    
    // Only display toast notification (don't emit event)
    toast.show('Einladungslink wurde versendet.', 'success');
  } catch (error) {
    console.error('Error sending invitation:', error);
    toast.show('Fehler beim Versand des Einladungslinks.', 'error');
  } finally { 
    isSending.value = false;
  }
};

const finish = () => {
  // Only emit the finish event when explicitly finished
  emit('finish');
};

const fetchRoles = async () => {
  try {
    const rolesResponse = await getRoles();
    roles.value = Object.values(rolesResponse.data).map(role => ({
      value: role.id,
      label: role.name
    }));
  } catch (error) {
    console.error('Failed to fetch roles:', error);
    throw error;
  }
};

const fetchPermissions = async () => {
  try {
    const permissionsResponse = await getPermissions();
    permissions.value = permissionsResponse;

    permissionsArchive.value = permissions.value.archive.map(p => ({
      value: p.id,
      label: p.display_name
    }));

    permissionsArchiveEdit.value = permissions.value.archive_edit.map(p => ({
      value: p.id,
      label: p.display_name
    }));

    permissionsCard.value = permissions.value.card.map(p => ({
      value: p.id,
      label: p.display_name
    }));

    permissionsCardEdit.value = permissions.value.card_edit.map(p => ({
      value: p.id,
      label: p.display_name
    }));
  } catch (error) {
    console.error('Failed to fetch permissions:', error);
    throw error;
  }
};

const fetchRolesWithPermissions = async () => {
  try {
    const permissionsByRoleResponse = await getRolesWithPermissions();
    permissionsByRole.value = permissionsByRoleResponse;
  } catch (error) {
    console.error('Failed to fetch roles with permissions:', error);
    throw error;
  }
};

const handleRoleChange = () => {
  // Find the selected role in permissionsByRole
  const role = Object.values(permissionsByRole.value)
    .find(r => r.id == form.value.role);
  
  if (!role) {
    console.warn(`Role with ID ${form.value.role} not found`);
    return;
  }
  
  // Extract permissions
  const rolePermissions = role.permissions || [];
  const permissionIds = rolePermissions.map(p => p.id);
  
  // Set the selected permissions in the form
  form.value.selectedPermissions = permissionIds;
};
</script>