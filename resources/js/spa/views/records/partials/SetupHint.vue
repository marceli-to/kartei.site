<template>
  <InfoBox
    default-class="bg-(--theme-color) p-8 text-sm"
    :closable="false">
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
  </InfoBox>
</template>
<script setup>
// Same box as the other hints (InfoBox, 14px), but in the column instead of floating beside it
import Icon from '@/components/icons/Icon.vue';
import InfoBox from '@/components/infobox/InfoBox.vue';

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
