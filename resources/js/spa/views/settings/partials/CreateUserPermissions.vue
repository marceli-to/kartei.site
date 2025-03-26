<template>
  <form 
    @submit.prevent="submit"
    class="w-full h-full flex flex-col justify-between"
    v-if="!isLoading">
    <div class="flex flex-col gap-y-20">
      <!-- Archive Selection -->
      <InputGroup>
        <InputLabel label="Kartei" id="archive_selection" />
        <InputSelect
          id="archive_selection"
          v-model="selectedArchive"
          :options="archiveOptions"
          placeholder="Kartei auswählen"
          @change="handleArchiveChange"
        />
      </InputGroup>

      <div v-if="selectedArchive" class="flex flex-col gap-y-20">
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

        <InputGroup>
          <InputLabel label="Kartei" id="archive" />
          <InputSelectButtons
            id="archive"
            v-model="form.selectedPermissions"
            :multiple="true"
            name="permissions"
            wrapperClasses="grid grid-cols-2 gap-8"
            :options="permissionsArchive" />
        </InputGroup>

        <InputGroup>
          <InputLabel label="Bearbeiten" id="archive_edit" />
          <InputSelectButtons
            id="archive_edit"
            v-model="form.selectedPermissions"
            :multiple="true"
            name="permissions"
            wrapperClasses="grid grid-cols-2 gap-8"
            :options="permissionsArchiveEdit" />
        </InputGroup>

        <InputGroup>
          <InputLabel label="Karten" id="card" />
          <InputSelectButtons
            id="card"
            v-model="form.selectedPermissions"
            :multiple="true"
            name="permissions"
            wrapperClasses="grid grid-cols-2 gap-8"
            :options="permissionsCard" />
        </InputGroup>

        <InputGroup>
          <InputLabel label="Bearbeiten" id="card_edit" />
          <InputSelectButtons
            id="card_edit"
            v-model="form.selectedPermissions"
            :multiple="true"
            name="permissions"
            wrapperClasses="grid grid-cols-2 gap-8"
            :options="permissionsCardEdit" />
        </InputGroup>
      </div>
    </div>
    <ButtonGroup>
      <ButtonPrimary @click="$emit('cancel')" label="Abbrechen" />
      <ButtonPrimary type="submit" label="Speichern" :disabled="isSaving" />
    </ButtonGroup>
  </form>
</template>
<script setup>
import { ref, onMounted, computed, provide } from 'vue';
import { useToastStore } from '@/components/toast/stores/toast';
import { getRoles, getRolesWithPermissions } from '@/services/api/role';
import { getPermissions } from '@/services/api/permission';
import { storePermissions } from '@/services/api/user';
import { getArchivesByAdmin } from '@/services/api/archive';
import InputGroup from '@/components/forms/Group.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputSelect from '@/components/forms/Select.vue';
import InputSelectButtons from '@/components/forms/SelectButtons.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';

const toast = useToastStore();
const roles = ref([]);
const permissions = ref([]);
const archives = ref([]);
const selectedArchive = ref('');

const permissionsArchive = ref([]);
const permissionsArchiveEdit = ref([]);
const permissionsCard = ref([]);
const permissionsCardEdit = ref([]);
const permissionsByRole = ref([]);

// Archive permissions map to track permissions for each archive
const archivePermissionsMap = ref({});

const props = defineProps({
  user: {
    type: Object,
    required: true
  }
});

const form = ref({
  role: 3, // Manager as default
  selectedPermissions: []
});

const errors = ref({
  role: null
});

const isLoading = ref(true);
const isSaving = ref(false);

const emit = defineEmits(['success', 'cancel']);

const archiveOptions = computed(() => {
  return archives.value.map(archive => ({
    value: archive.uuid,
    label: archive.title
  }));
});

// Provide the permissions map to child components
provide('archivePermissionsMap', archivePermissionsMap);

onMounted(async () => {
  try {
    isLoading.value = true;
    await fetchArchives();
    await fetchRoles();
    await fetchPermissions();
    await fetchRolesWithPermissions();
    
    // Select the first archive by default if available
    if (archives.value.length > 0) {
      selectedArchive.value = archives.value[0].uuid;
      // Make sure to set default permissions for the Manager role
      setTimeout(() => {
        handleRoleChange();
        handleArchiveChange();
      }, 0);
    }
  }
  catch (error) {
    console.error(error);
  } 
  finally {
    isLoading.value = false;
  }
});

const fetchArchives = async () => {
  try {
    const archivesResponse = await getArchivesByAdmin();
    archives.value = archivesResponse;
    
    // Initialize permissions map for each archive but don't set any default permissions yet
    // We'll set them when the archive is selected
    archives.value.forEach(archive => {
      archivePermissionsMap.value[archive.uuid] = {
        role: 3,
        selectedPermissions: []
      };
    });
  } catch (error) {
    console.error('Failed to fetch archives:', error);
  }
};

const handleArchiveChange = () => {
  if (selectedArchive.value) {
    // Load permissions for the selected archive if they exist
    if (archivePermissionsMap.value[selectedArchive.value] && 
        archivePermissionsMap.value[selectedArchive.value].selectedPermissions.length > 0) {
      // Archive already has permissions set
      form.value.role = archivePermissionsMap.value[selectedArchive.value].role;
      form.value.selectedPermissions = [...archivePermissionsMap.value[selectedArchive.value].selectedPermissions];
    } else {
      // Set default role and permissions for this archive
      form.value.role = 3; // Manager as default
      
      // Find the role and set its permissions
      setTimeout(() => {
        handleRoleChange();
      }, 0);
    }
  }
};

// Helper method to save current selections to the map
const saveCurrentPermissionsToMap = () => {
  const previousArchive = selectedArchive.value;
  if (previousArchive && archivePermissionsMap.value[previousArchive]) {
    archivePermissionsMap.value[previousArchive] = {
      role: form.value.role,
      selectedPermissions: [...form.value.selectedPermissions]
    };
  }
};

const submit = async () => {
  try {
    // Save current archive permissions to the map
    saveCurrentPermissionsToMap();
    
    // Check if permissions are set for all archives
    let missingPermissions = false;
    const archivesWithMissingPermissions = [];
    
    for (const archiveId in archivePermissionsMap.value) {
      // Check if role is selected and permissions are set
      if (!archivePermissionsMap.value[archiveId].role || 
          !archivePermissionsMap.value[archiveId].selectedPermissions || 
          archivePermissionsMap.value[archiveId].selectedPermissions.length === 0) {
        missingPermissions = true;
        
        // Find archive name for more helpful error message
        const archiveName = archives.value.find(a => a.uuid === archiveId)?.title || archiveId;
        archivesWithMissingPermissions.push(archiveName);
      }
    }
    
    // If any archive is missing permissions, show toast and exit
    if (missingPermissions) {
      toast.show('Bitte Rollen/Berechtigungen für alle Karteien wählen.', 'error');
      return;
    }
    
    // If all validations pass, proceed with saving
    isSaving.value = true;
    
    // Prepare data for submission - one archive at a time
    const permissionsToSubmit = [];
    
    for (const archiveId in archivePermissionsMap.value) {
      permissionsToSubmit.push({
        archive: archiveId,
        role: archivePermissionsMap.value[archiveId].role,
        selectedPermissions: archivePermissionsMap.value[archiveId].selectedPermissions
      });
    }
    
    // Submit all archive permissions
    const response = await storePermissions(props.user, { permissions: permissionsToSubmit });
    emit('success', response);
  }
  catch (error) {
    errors.value = {
      role: error.response?.data?.errors?.role?.[0]
    };
    
    toast.show('Fehler beim Speichern der Berechtigungen.', 'error');
  }
  finally {
    isSaving.value = false;
  }
};

const fetchRoles = async () => {
  const rolesResponse = await getRoles();
  roles.value = Object.values(rolesResponse.data).map(role => ({
    value: role.id,
    label: role.name
  }));
};

const fetchPermissions = async () => {
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
};

const fetchRolesWithPermissions = async () => {
  const permissionsByRoleResponse = await getRolesWithPermissions();
  permissionsByRole.value = permissionsByRoleResponse;
};

const handleRoleChange = () => {
  // First save the current permissions if we're changing from one role to another
  if (selectedArchive.value) {
    const previousRole = archivePermissionsMap.value[selectedArchive.value]?.role;
    
    // If we had a previous role and it's different from the current one,
    // save the permissions for that role
    if (previousRole && previousRole !== form.value.role) {
      saveCurrentPermissionsToMap();
    }
  }
  
  let role = null;
  for (const key in permissionsByRole.value) {
    if (permissionsByRole.value[key].id == form.value.role) {
      role = permissionsByRole.value[key];
      break;
    }
  }
  
  // Extract permissions if the role exists
  const permissions = role?.permissions || [];
  const permissionIds = permissions.map(p => p.id);
  
  // Set the selected permissions in the form
  form.value.selectedPermissions = permissionIds;
  
  // Save to the map if archive is selected
  if (selectedArchive.value) {
    archivePermissionsMap.value[selectedArchive.value] = {
      role: form.value.role,
      selectedPermissions: [...form.value.selectedPermissions]
    };
  }
};
</script>