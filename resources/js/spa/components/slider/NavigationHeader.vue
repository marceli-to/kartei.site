<!-- NavigationHeader.vue -->
<template>
  <h2 class="flex justify-between items-center mb-38 mr-8">
    <span>{{ title }}</span>
    <router-link :to="linkTarget">
      <IconCross variant="small" />
    </router-link>
  </h2>
</template>
<script setup>
import { computed } from 'vue';
import IconCross from '@/components/icons/Cross.vue';

const props = defineProps({
  title: { type: String, default: 'Voreinstellungen' },
  uuid: {
    type: String,
    default: null
  },
  archive: {
    type: Object,
    default: null
  }
});

const linkTarget = computed(() =>
  props.archive
    ? { name: 'archiveRecords', params: { uuid: props.uuid } }
    : { name: 'archives' }
);
</script>
