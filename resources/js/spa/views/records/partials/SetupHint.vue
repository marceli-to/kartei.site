<template>
  <div class="bg-(--theme-color) px-8 pt-8 pb-12 text-xs leading-16">
    <!-- Hint in the record form for something the archive doesn't have yet (KAR0001_Hints_261108), with a link to its settings -->
    <p><slot /></p>
    <router-link
      v-if="canEdit"
      :to="{ name: 'archiveSettings', params: { uuid: archiveUuid }, query: { section } }"
      class="flex items-center gap-x-8 font-muoto-medium">
      <Icon
        name="chevron-right"
        variant="tiny" />
      Zur Voreinstellung «{{ label }}»
    </router-link>
  </div>
</template>
<script setup>
import Icon from '@/components/icons/Icon.vue';

defineProps({
  archiveUuid: {
    type: String,
    required: true,
  },
  // Slide id in the archive settings: structure, card, tags
  section: {
    type: String,
    required: true,
  },
  // Slide name, as in the link text
  label: {
    type: String,
    required: true,
  },
  // The user may change that setting; otherwise the hint goes without the link
  canEdit: {
    type: Boolean,
    default: false,
  },
});
</script>
