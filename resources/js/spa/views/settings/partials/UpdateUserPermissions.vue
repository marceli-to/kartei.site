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
              <IconCheckmark v-if="isSaved(option.value) || hasPermissions(option.value)" />
            </template>
        </InputSelectButtons>
      </InputGroup>
      <div :class="{'opacity-20 select-none pointer-events-none': !selectedArchiveId}" class="flex flex-col gap-y-20">
        <template v-if="!isSaved(selectedArchiveId)">
          <InputGroup>
            <InputLabel :label="roleLabel" id="role" />
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
          <div v-if="isNewlyAddedArchive && !invitationSent[selectedArchiveId]">
            <ButtonAuth 
              :label="isSending ? 'Wird versendet...' : 'Einladungslink versenden'" 
              @click="send" 
              :disabled="isSending" />
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
</template>
<script setup>
import { ref, computed, provide, watch, onMounted } from 'vue';
import { useToastStore } from '@/components/toast/stores/toast';
import { useDialogStore } from '@/components/dialog/stores/dialog';
import { getRoles, getRolesWithPermissions } from '@/services/api/role';
import { getPermissions, getPermissionsByUser } from '@/services/api/permission';
import { updatePermissions, sendInvitation } from '@/services/api/user';
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

const DEFAULT_ROLE = 5;

// Props
const props = defineProps({
  user: {
    type: Object,
    required: true
  }
});

// Emits
const emit = defineEmits(['finish', 'cancel', 'success']);

// Core state
const isLoading = ref(true);
const isSaving = ref(false);
const isSending = ref(false);
const selectedArchiveId = ref('');
const currentArchiveId = ref(null);
const archives = ref([]);
const roles = ref([]);
const permissions = ref({});
const permissionsByRole = ref({});
const userPermissions = ref({});
const invitationSent = ref({});
const newlyAddedArchives = ref({});

// Permission categories
const permissionsArchive = ref([]);
const permissionsArchiveEdit = ref([]);
const permissionsCard = ref([]);
const permissionsCardEdit = ref([]);

// Error handling
const errors = ref({
  role: null
});

// Internal permission state
const archivePermissions = ref({});

// Computed
const roleLabel = computed(() => {
  if (!selectedArchiveId.value) return 'Rechte';

  const archiveData = archivePermissions.value[selectedArchiveId.value];
  if (!archiveData) return 'Rechte';

  const roleId = archiveData.role;
  const selected = [...archiveData.selectedPermissions].sort();

  const role = Object.values(permissionsByRole.value).find(r => r.id == roleId);
  if (!role) return 'Rechte';

  const rolePermissions = role.permissions.map(p => Number(p.id)).sort();

  const isAdjusted =
    selected.length !== rolePermissions.length ||
    !selected.every((val, idx) => val === rolePermissions[idx]);

  return isAdjusted ? 'Rechte (angepasst)' : 'Rechte';
});

const archiveOptions = computed(() => {
  return archives.value.map(archive => ({
    value: archive.uuid,
    label: archive.name,
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

  const original = [...archiveData.originalPermissions].sort();
  const current = [...archiveData.selectedPermissions].sort();

  if (archiveData.isDirty) return true;
  if (archiveData.role !== archiveData.originalRole) return true;
  if (original.length !== current.length) return true;
  return !original.every((val, idx) => val === current[idx]);
});

const hasUnhandledArchives = computed(() => {
  return archives.value.some(archive => !isSaved(archive.uuid));
});

const isNewlyAddedArchive = computed(() => {
  return newlyAddedArchives.value[selectedArchiveId.value] === true;
});

// Methods
function isSaved(archiveId) {
  if (!archiveId) return false;
  return archivePermissions.value[archiveId]?.isSaved || false;
}

function hasPermissions(archiveId) {
  const perms = archivePermissions.value[archiveId]?.selectedPermissions || [];
  return perms.length > 0;
}

async function selectArchive(archiveId) {
  if (archiveId !== currentArchiveId.value) {
    let hasUnsavedChanges = false;

    if (currentArchiveId.value) {
      const archiveData = archivePermissions.value[currentArchiveId.value];
      if (archiveData) {
        const original = [...archiveData.originalPermissions].sort();
        const current = [...archiveData.selectedPermissions].sort();
        hasUnsavedChanges =
          archiveData.isDirty ||
          archiveData.role !== archiveData.originalRole ||
          original.length !== current.length ||
          !original.every((val, idx) => val === current[idx]);
      }
    }

    if (currentArchiveId.value && hasUnsavedChanges) {
      const shouldDiscard = await confirmDiscardChanges();
      if (!shouldDiscard) {
        selectedArchiveId.value = currentArchiveId.value;
        return;
      }
    }

    selectedArchiveId.value = archiveId;
    currentArchiveId.value = archiveId;

    if (archiveId) {
      const hasExistingPermissions = userPermissions.value[archiveId] !== undefined;

      if (hasExistingPermissions) {
        const existingPermissionData = userPermissions.value[archiveId];
        const role = existingPermissionData.role || DEFAULT_ROLE;
        const perms = existingPermissionData.permissions || [];
        const normalizedPermissions = existingPermissionData.permissions || [];
        const shouldSetDefaults = normalizedPermissions.length === 0;

        archivePermissions.value[archiveId] = {
          role,
          selectedPermissions: shouldSetDefaults ? [] : normalizedPermissions,
          isDirty: shouldSetDefaults, // mark dirty if we’re going to inject defaults
          isSaved: false,
          originalRole: role,
          originalPermissions: shouldSetDefaults ? [] : [...normalizedPermissions]
        };

        // If there are no permissions from the backend, apply role defaults now
        if (shouldSetDefaults && permissionsByRole.value && Object.keys(permissionsByRole.value).length > 0) {
          setDefaultPermissionsForRole(role, archiveId);
        }
      }
      else {
        if (!archivePermissions.value[archiveId]) {
          archivePermissions.value[archiveId] = {
            role: DEFAULT_ROLE,
            selectedPermissions: [],
            isDirty: true,
            isSaved: false,
            originalRole: DEFAULT_ROLE,
            originalPermissions: []
          };
        }

        if (Object.keys(permissionsByRole.value).length > 0) {
          setDefaultPermissionsForRole(DEFAULT_ROLE, archiveId);
        } else {
          const unwatch = watch(permissionsByRole, () => {
            if (Object.keys(permissionsByRole.value).length > 0) {
              setDefaultPermissionsForRole(DEFAULT_ROLE, archiveId);
              unwatch();
            }
          }, { immediate: true });
        }
      }
    }
  }
}

function updateRole(roleId) {
  if (!selectedArchiveId.value) return;

  if (typeof roleId === 'object' && roleId.target) {
    roleId = roleId.target.value;
  }
  if (typeof roleId === 'string') {
    roleId = parseInt(roleId, 10);
  }

  currentRole.value = roleId;
  setDefaultPermissionsForRole(roleId, selectedArchiveId.value);
}

function setDefaultPermissionsForRole(roleId, archiveId) {
  const role = Object.values(permissionsByRole.value).find(r => r.id == roleId);
  if (!role) return;

  const permissionIds = role.permissions.map(p => Number(Number(p.id)));
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
      onConfirm: () => resolve(true),
      onCancel: () => resolve(false)
    });
  });
}

async function submit() {
  try {
    if (!validateForm()) return;
    isSaving.value = true;

    const data = {
      archive: selectedArchiveId.value,
      role: currentRole.value,
      permissions: currentPermissions.value
    };

    await updatePermissions(props.user, data);

    const archiveData = archivePermissions.value[selectedArchiveId.value];
    archiveData.isSaved = true;
    archiveData.isDirty = false;
    archiveData.originalRole = currentRole.value;
    archiveData.originalPermissions = [...currentPermissions.value];

    const wasNew = !userPermissions.value[selectedArchiveId.value]; // 👈 check BEFORE update

    userPermissions.value[selectedArchiveId.value] = {
      role: currentRole.value,
      permissions: [...currentPermissions.value]
    };
    // selectedArchiveId.value = '';
    currentArchiveId.value = null;
    toast.show('Berechtigungen wurden gespeichert.', 'success');
    if (wasNew) {
      newlyAddedArchives.value[selectedArchiveId.value] = true; // ✅ set after update
    }
  } 
  catch (error) {
    console.error('Failed to save permissions:', error);
    toast.show('Fehler beim Speichern der Berechtigungen.', 'error');
  } 
  finally {
    isSaving.value = false;
  }
}

async function send() {
  if (!selectedArchiveId.value) return;

  try {
    isSending.value = true;
    await sendInvitation(props.user, { archive: selectedArchiveId.value });
    invitationSent.value[selectedArchiveId.value] = true;
    selectedArchiveId.value = '';
    toast.show('Einladungslink wurde versendet.', 'success');
  } catch (error) {
    console.error('Failed to send invitation:', error);
    toast.show('Fehler beim Versenden des Einladungslinks.', 'error');
  } finally {
    isSending.value = false;
  }
}

function validateForm() {
  errors.value = { role: null };

  if (!selectedArchiveId.value) {
    toast.show('Bitte wähle eine Kartei aus.', 'error');
    return false;
  }
  if (currentPermissions.value.length === 0) {
    toast.show('Bitte wähle mindestens eine Berechtigung aus.', 'error');
    return false;
  }
  return true;
}

onMounted(async () => {
  try {
    isLoading.value = true;
    await Promise.all([
      fetchArchives(),
      fetchRoles(),
      fetchPermissions(),
      fetchRolesWithPermissions(),
      fetchUserPermissions()
    ]);
    applyUserPermissions();
  } catch (error) {
    console.error('Error initializing component:', error);
    toast.show('Fehler beim Laden der Daten.', 'error');
  } finally {
    isLoading.value = false;
  }
});

async function fetchArchives() {
  const response = await getArchivesByAdmin();
  archives.value = response;
}

async function fetchRoles() {
  const rolesResponse = await getRoles();
  if (Array.isArray(rolesResponse)) {
    roles.value = rolesResponse;
  } else if (rolesResponse?.data) {
    roles.value = rolesResponse.data.map(role => ({
      value: role.id,
      label: role.name
    }));
  }
}

async function fetchPermissions() {
  const permissionsResponse = await getPermissions();
  permissions.value = permissionsResponse;

  permissionsArchive.value = [];
  permissionsArchiveEdit.value = [];
  permissionsCard.value = [];
  permissionsCardEdit.value = [];

  if (permissions.value.archive) {
    permissionsArchive.value = permissions.value.archive.map(p => ({ value: Number(p.id), label: p.display_name }));
    permissionsArchiveEdit.value = permissions.value.archive_edit.map(p => ({ value: Number(p.id), label: p.display_name }));
    permissionsCard.value = permissions.value.card.map(p => ({ value: Number(p.id), label: p.display_name }));
    permissionsCardEdit.value = permissions.value.card_edit.map(p => ({ value: Number(p.id), label: p.display_name }));
  }
}

async function fetchRolesWithPermissions() {
  const response = await getRolesWithPermissions();
  permissionsByRole.value = response || {};
}

async function fetchUserPermissions() {
  const response = await getPermissionsByUser(props.user);

  userPermissions.value = {};

  Object.entries(response || {}).forEach(([archiveId, data]) => {
    const role = data.role?.id || DEFAULT_ROLE;
    const permissions = Array.isArray(data.permissions)
      ? data.permissions.map(p => Number(p.id))
      : [];

    userPermissions.value[archiveId] = {
      role,
      permissions
    };
  });
}

function applyUserPermissions() {
  Object.entries(userPermissions.value || {}).forEach(([archiveId, permData]) => {
    const role = permData.role || DEFAULT_ROLE;
    const perms = permData.permissions || [];
    const normalizedPermissions = permData.permissions || [];
    const shouldSetDefaults = normalizedPermissions.length === 0;

    archivePermissions.value[archiveId] = {
      role,
      selectedPermissions: shouldSetDefaults ? [] : normalizedPermissions,
      isDirty: shouldSetDefaults,
      isSaved: false,
      originalRole: role,
      originalPermissions: shouldSetDefaults ? [] : [...normalizedPermissions]
    };

    // Automatically apply default permissions if backend returned none
    if (shouldSetDefaults && Object.keys(permissionsByRole.value).length > 0) {
      setDefaultPermissionsForRole(role, archiveId);
    }
  });
}

provide('archivePermissions', archivePermissions);
</script>
