<template>
  <!-- Editing a user: one box with the tabs Kontakt (name, email) and Rechte (permissions per archive) -->
  <EditBox
    :title="`${user.firstname} ${user.name}`"
    fill
    @close="$emit('cancel')">
    <InputSelectButtons
      id="user_edit_tab"
      v-model="tab"
      legend="Bereich"
      name="user_edit_tab"
      wrapper-classes="flex flex-col gap-y-8"
      :options="TABS" />
    <!-- v-show keeps unsaved input when switching tabs -->
    <UserForm
      v-show="tab === 'contact'"
      :uuid="user.uuid"
      class="mt-20 h-auto! grow"
      @success="$emit('success')"
      @user-deleted="(uuid) => $emit('user-deleted', uuid)"
      @cancel="$emit('cancel')" />
    <UserPermissions
      v-show="tab === 'permissions'"
      :user="user"
      :archive-uuid="archiveUuid"
      class="mt-20 h-auto! grow"
      @success="$emit('success')" />
  </EditBox>
</template>

<script setup>
import { ref } from 'vue';
import EditBox from '@/components/EditBox.vue';
import InputSelectButtons from '@/components/forms/SelectButtons.vue';
import UserForm from '@/views/users/partials/UserForm.vue';
import UserPermissions from '@/views/users/partials/UserPermissions.vue';

defineProps({
  user: { type: Object, required: true },
  archiveUuid: { type: String, default: null },
});

defineEmits(['success', 'cancel', 'user-deleted']);

const TABS = [
  { value: 'contact', label: 'Kontakt' },
  { value: 'permissions', label: 'Rechte' },
];

const tab = ref('contact');
</script>
