<template>
  <form 
    @submit.prevent="submit"
    class="w-full h-full flex flex-col justify-between"
    v-if="!isLoading">
    <div class="flex flex-col gap-y-20">
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
    <ButtonGroup>
      <ButtonPrimary @click="$emit('cancel')" label="Abbrechen" />
      <ButtonPrimary type="submit" label="Speichern" :disabled="isSaving" />
    </ButtonGroup>
  </form>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import { useToastStore } from '@/components/toast/stores/toast';
import { getRoles, getRolesWithPermissions } from '@/services/api/role';
import { getPermissions } from '@/services/api/permission';
import { storePermissions } from '@/services/api/user';
import InputGroup from '@/components/forms/Group.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputSelect from '@/components/forms/Select.vue';
import InputSelectButtons from '@/components/forms/SelectButtons.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';

const toast = useToastStore();
const roles = ref([]);
const permissions = ref([]);

const permissionsArchive = ref([]);
const permissionsArchiveEdit = ref([]);
const permissionsCard = ref([]);
const permissionsCardEdit = ref([]);
const permissionsByRole = ref([]);

const props = defineProps({
  user: {
    type: Object,
    required: true
  },
  selectedArchives: {
    type: Array,
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

onMounted(async () => {
  try {
    isLoading.value = true;
    await fetchRoles();
    await fetchPermissions();
    await fetchRolesWithPermissions();
  }
  catch (error) {
    console.error(error);
  } 
  finally {
    isLoading.value = false;
  }
});

const submit = async () => {
  try {
    isSaving.value = true;
    const permissionsData = {
      ...form.value,
      archives: props.selectedArchives
    };
    const response = await storePermissions(props.user, permissionsData);
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
}

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
  handleRoleChange();
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
};
</script>
