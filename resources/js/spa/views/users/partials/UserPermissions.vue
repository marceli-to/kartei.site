<template>
  <form
    v-if="!isLoading"
    @submit.prevent="save"
    class="w-full h-full flex flex-col justify-between">
    <div class="flex flex-col gap-y-20">
      <InputGroup v-if="!archiveUuid">
        <InputLabel label="Kartei" id="archive_selection" />
        <InputSelectButtons
          id="archive_selection"
          :modelValue="selected"
          @update:modelValue="selectArchive"
          name="archive_selection"
          wrapperClasses="flex flex-col gap-y-8"
          :options="archiveOptions">
          <template #icon="{ option }">
            <IconCheckmark v-if="saved[option.value]" />
          </template>
        </InputSelectButtons>
      </InputGroup>

      <div
        :class="{ 'opacity-20 select-none pointer-events-none': !selected }"
        class="flex flex-col gap-y-20">

        <!-- After saving: invite a user who was just added, or confirm -->
        <div v-if="isDone" :class="{ 'relative top-8': archiveUuid }">
          <template v-if="added.has(selected) && !invited.has(selected)">
            <ButtonAuth
              type="button"
              :label="isSending ? 'Wird versendet...' : 'Einladungslink versenden'"
              :disabled="isSending"
              @click="invite" />
            <p class="text-sm p-8 mt-8">Mit Abschicken des Einladungslinks erhält {{ user.firstname }} {{ user.name }} Zugang zu den ausgewählten Karteien. Zugriffsrechte können innerhalb der Kartei in den Voreinstellungen angepasst werden.</p>
          </template>
          <p v-else class="text-sm p-8 mt-8">
            {{ invited.has(selected) ? 'Einladungslink wurde versendet.' : 'Berechtigungen wurden gespeichert.' }}
            <template v-if="!archiveUuid && hasArchivesWithoutAccess">
              Sie können Berechtigungen für weitere Karteien setzen oder den Prozess abschliessen.
            </template>
          </p>
        </div>

        <template v-else>
          <InputGroup>
            <InputLabel :label="isAdjusted ? 'Rechte (angepasst)' : 'Rechte'" id="role" />
            <InputSelect
              id="role"
              :modelValue="form.role"
              @update:modelValue="changeRole"
              :options="roleOptions" />
          </InputGroup>
          <InputGroup v-for="group in permissionGroups" :key="group.key">
            <InputLabel :label="group.label" :id="`permissions_${group.key}`" />
            <InputSelectButtons
              :id="`permissions_${group.key}`"
              v-model="form.permissions"
              :multiple="true"
              name="permissions"
              wrapperClasses="grid grid-cols-2 gap-8"
              :options="group.options" />
          </InputGroup>
        </template>
      </div>
    </div>

    <ButtonGroup>
      <ButtonPrimary
        v-if="isDone"
        type="button"
        label="Abschliessen"
        :disabled="isSending"
        @click="emit('success')" />
      <template v-else>
        <ButtonPrimary
          type="submit"
          label="Speichern"
          :disabled="isSaving || !canSave"
          :loading="isSaving" />
        <ButtonPrimary
          v-if="cancelable"
          type="button"
          label="Abbrechen"
          @click="emit('cancel')" />
      </template>
    </ButtonGroup>
  </form>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue';
import { useToastStore } from '@/components/toast/stores/toast';
import { useDialogStore } from '@/components/dialog/stores/dialog';
import { getRoles, getRolesWithPermissions } from '@/services/api/role';
import { getPermissions, getPermissionsByUser } from '@/services/api/permission';
import { storePermissions, sendInvitation } from '@/services/api/user';
import { getArchivesByAdmin, getArchive } from '@/services/api/archive';
import InputGroup from '@/components/forms/Group.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputSelect from '@/components/forms/Select.vue';
import InputSelectButtons from '@/components/forms/SelectButtons.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import ButtonAuth from '@/components/buttons/Auth.vue';
import IconCheckmark from '@/components/icons/Checkmark.vue';

// New assignments start with the role giving the least access
const DEFAULT_ROLE_NAME = 'Viewer';

const props = defineProps({
  user: { type: Object, required: true },
  // Limits the form to this archive (opened from within an archive)
  archiveUuid: { type: String, default: null },
  cancelable: { type: Boolean, default: false },
});

const emit = defineEmits(['success', 'cancel']);

const toast = useToastStore();
const dialogStore = useDialogStore();

const isLoading = ref(true);
const isSaving = ref(false);
const isSending = ref(false);

const archives = ref([]);
const roleOptions = ref([]);
const permissionGroups = ref([]);
const roleDefaults = ref({});   // role id => permission ids
const defaultRole = ref(null);

const saved = ref({});          // archive uuid => { role, permissions } as stored on the server
const added = reactive(new Set());   // archives the user was added to in this session
const invited = reactive(new Set()); // archives an invitation was sent for
const selected = ref('');
const isDone = ref(false);      // the selected archive was just saved
const form = ref({ role: null, permissions: [] });

const archiveOptions = computed(() =>
  archives.value.map(archive => ({ value: archive.uuid, label: archive.name }))
);

const hasArchivesWithoutAccess = computed(() =>
  archives.value.some(archive => !saved.value[archive.uuid])
);

const isAdjusted = computed(() =>
  !sameSet(form.value.permissions, roleDefaults.value[form.value.role] ?? [])
);

// What the form would show if left untouched
const baseline = computed(() => saved.value[selected.value] ?? defaultsFor(defaultRole.value));

const isDirty = computed(() =>
  !!selected.value && !isDone.value && (
    form.value.role !== baseline.value.role ||
    !sameSet(form.value.permissions, baseline.value.permissions)
  )
);

// An untouched new assignment can be saved as is
const canSave = computed(() =>
  !!selected.value && form.value.permissions.length > 0 && (isDirty.value || !saved.value[selected.value])
);

function sameSet(a, b) {
  return a.length === b.length && a.every(id => b.includes(id));
}

function defaultsFor(role) {
  return { role, permissions: [...(roleDefaults.value[role] ?? [])] };
}

async function selectArchive(uuid) {
  // Clicking the selected archive again only matters to reopen its form after saving
  if (uuid === selected.value && !isDone.value) return;
  if (isDirty.value && !(await confirmDiscard())) return;

  selected.value = uuid;
  const current = saved.value[uuid];
  form.value = current
    ? { role: current.role, permissions: [...current.permissions] }
    : defaultsFor(defaultRole.value);

  // An archive saved in this session but not yet invited goes back to the invitation
  isDone.value = added.has(uuid) && !invited.has(uuid);
}

function changeRole(role) {
  form.value = defaultsFor(Number(role));
}

function confirmDiscard() {
  return new Promise(resolve => {
    dialogStore.show({
      title: 'Ungespeicherte Änderungen',
      message: 'Sie haben ungespeicherte Änderungen. Möchten Sie diese verwerfen?',
      confirmLabel: 'Verwerfen',
      cancelLabel: 'Abbrechen',
      size: 'small',
      onConfirm: () => resolve(true),
      onCancel: () => resolve(false),
    });
  });
}

async function save() {
  if (!canSave.value) return;

  try {
    isSaving.value = true;
    await storePermissions(props.user, { archive: selected.value, ...form.value });

    const isNew = !saved.value[selected.value];
    saved.value[selected.value] = { role: form.value.role, permissions: [...form.value.permissions] };
    toast.show('Berechtigungen wurden gespeichert.', 'success');

    if (isNew) {
      added.add(selected.value);
    } else if (props.archiveUuid) {
      // Nothing left to do in a single archive
      emit('success');
      return;
    }
    isDone.value = true;
  }
  catch (error) {
    console.error('Failed to save permissions:', error);
    toast.show('Fehler beim Speichern der Berechtigungen.', 'error');
  }
  finally {
    isSaving.value = false;
  }
}

async function invite() {
  try {
    isSending.value = true;
    await sendInvitation(props.user, { archive: selected.value });
    invited.add(selected.value);
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

onMounted(async () => {
  try {
    const [archiveList, roles, rolesWithPermissions, permissions, userPermissions] = await Promise.all([
      props.archiveUuid ? getArchive(props.archiveUuid).then(a => [a]) : getArchivesByAdmin(),
      getRoles(),
      getRolesWithPermissions(),
      getPermissions(),
      getPermissionsByUser(props.user),
    ]);

    archives.value = archiveList;
    roleOptions.value = roles.data.map(role => ({ value: role.id, label: role.name }));

    for (const role of Object.values(rolesWithPermissions)) {
      roleDefaults.value[role.id] = role.permissions.map(p => p.id);
      if (role.name === DEFAULT_ROLE_NAME) defaultRole.value = role.id;
    }

    // Grouped by group_key, in display order
    permissionGroups.value = Object.values(permissions).map(group => ({
      key: group[0].group_key,
      label: group[0].group_name,
      options: group.map(p => ({ value: p.id, label: p.display_name })),
    }));

    saved.value = userPermissions;

    if (props.archiveUuid) {
      await selectArchive(props.archiveUuid);
    }
  }
  catch (error) {
    console.error('Failed to load permissions:', error);
    toast.show('Fehler beim Laden der Berechtigungen.', 'error');
  }
  finally {
    isLoading.value = false;
  }
});
</script>
