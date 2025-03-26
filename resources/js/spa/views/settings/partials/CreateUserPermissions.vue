<template>
  <form 
    @submit.prevent="submit"
    class="w-full h-full flex flex-col justify-between"
    v-if="!isLoading">
    <div class="flex flex-col gap-y-20">
      <InputGroup>
        <InputLabel :label="archiveOptions.length > 1 ? 'Kartei/en' : 'Kartei'" id="archive_selection" />
        <InputSelectButtons
          id="archive_selection"
          v-model="selectedArchives"
          :multiple="true"
          name="archive_selection"
          wrapperClasses="flex flex-col gap-y-8"
          :options="archiveOptions"
          @change="handleArchivesChange"
        />
      </InputGroup>

      <div :class="{'opacity-20 select-none pointer-events-none': selectedArchives.length === 0}" class="flex flex-col gap-y-20">
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
      <ButtonPrimary type="submit" label="Speichern" :disabled="isSaving || selectedArchives.length === 0" />
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
const selectedArchives = ref([]);

const permissionsArchive = ref([]);
const permissionsArchiveEdit = ref([]);
const permissionsCard = ref([]);
const permissionsCardEdit = ref([]);
const permissionsByRole = ref([]);

const defaultRole = 3;

// Archive permissions map to track permissions for each archive
const archivePermissionsMap = ref({});

const props = defineProps({
  user: {
    type: Object,
    required: true
  }
});

const form = ref({
  role: defaultRole,
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
      selectedArchives.value = [archives.value[0].uuid];
      // Make sure to set default permissions for the Manager role
      setTimeout(() => {
        handleRoleChange();
        handleArchivesChange();
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
        role: defaultRole,
        selectedPermissions: []
      };
    });
  } catch (error) {
    console.error('Failed to fetch archives:', error);
  }
};

const handleArchivesChange = () => {
  if (selectedArchives.value.length > 0) {
    // Check if the newly selected archives have different permissions
    const firstArchive = selectedArchives.value[0];
    
    // Check if any selected archive has empty permissions
    const hasEmptyPermissions = selectedArchives.value.some(archiveId => {
      const archivePermissions = archivePermissionsMap.value[archiveId];
      return !archivePermissions.selectedPermissions || 
             archivePermissions.selectedPermissions.length === 0;
    });
    
    // Check if all selected archives have the same permissions
    const samePermissions = selectedArchives.value.every(archiveId => {
      const archivePermissions = archivePermissionsMap.value[archiveId];
      const firstArchivePermissions = archivePermissionsMap.value[firstArchive];
      
      // Check if permissions are the same (role and selectedPermissions)
      return archivePermissions.role === firstArchivePermissions.role && 
             arraysEqual(archivePermissions.selectedPermissions, firstArchivePermissions.selectedPermissions);
    });
    
    if (samePermissions && !hasEmptyPermissions) {
      // If all selected archives have the same permissions, use those
      form.value.role = archivePermissionsMap.value[firstArchive].role;
      form.value.selectedPermissions = [...archivePermissionsMap.value[firstArchive].selectedPermissions];
    } else {
      // If permissions differ or any archive has empty permissions, set default role and apply role permissions
      form.value.role = defaultRole; // Manager as default
      
      // Trigger role change to load default permissions for this role
      setTimeout(() => {
        handleRoleChange();
      }, 0);
    }
  } else {
    // No archives selected, reset form
    form.value.role = defaultRole;
    form.value.selectedPermissions = [];
  }
};

// Helper function to compare arrays
const arraysEqual = (a, b) => {
  if (a.length !== b.length) return false;
  const sortedA = [...a].sort();
  const sortedB = [...b].sort();
  return sortedA.every((val, idx) => val === sortedB[idx]);
};

// Helper method to save current selections to the map
const saveCurrentPermissionsToMap = () => {
  // Save the current permissions to all selected archives
  selectedArchives.value.forEach(archiveId => {
    if (archivePermissionsMap.value[archiveId]) {
      archivePermissionsMap.value[archiveId] = {
        role: form.value.role,
        selectedPermissions: [...form.value.selectedPermissions]
      };
    }
  });
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
  
  // Save to the map if archives are selected
  if (selectedArchives.value.length > 0) {
    // Update permissions for all selected archives
    selectedArchives.value.forEach(archiveId => {
      if (archivePermissionsMap.value[archiveId]) {
        archivePermissionsMap.value[archiveId] = {
          role: form.value.role,
          selectedPermissions: [...permissionIds]
        };
      }
    });
  }
};
</script>