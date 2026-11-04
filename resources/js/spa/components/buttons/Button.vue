<template>
  <button
    :type="type"
    :class="classes"
    :disabled="disabled || loading"
    :aria-busy="loading || undefined">
    <Icon
      v-if="variant === 'secondary'"
      name="chevron-right"
      variant="small" />
    {{ label }}
    <slot />
  </button>
</template>
<script setup>
import { computed } from 'vue';
import Icon from '@/components/icons/Icon.vue';

/**
 * Form button, imported as AppButton:
 *   <AppButton label="Speichern" :loading="isSaving" />
 *   <AppButton label="Löschen" variant="danger" type="button" @click="…" />
 *   <AppButton label="Passwort ändern" variant="secondary" type="button" @click="…" />
 */
const props = defineProps({
  type: {
    type: String,
    default: 'submit',
  },
  label: {
    type: String,
    required: true,
  },
  // primary: outlined (Speichern, Abbrechen); danger: red; secondary: filled
  // grey with a chevron, for an extra action inside a form (Passwort ändern)
  variant: {
    type: String,
    default: 'primary',
    validator: (value) => ['primary', 'danger', 'secondary'].includes(value),
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  // A request is running: disabled and marked busy
  loading: {
    type: Boolean,
    default: false,
  },
});

// The outline only shows for keyboard focus (focus-visible), not on click
const focus =
  'focus:outline-none focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black dark:focus-visible:outline-white';
const base = `min-h-default w-full flex items-center justify-center !ring-0 ${focus}`;

const VARIANTS = {
  primary: `${base} bg-white dark:bg-black border border-pebble text-graphite hover:text-black transition-all hover:theme-color hover:border-black disabled:hover:text-graphite disabled:hover:bg-transparent disabled:opacity-40 disabled:pointer-events-none`,
  danger: `${base} bg-white dark:bg-black text-flame hover:bg-flame hover:text-white border-flame border`,
  secondary: `${focus} border border-graphite min-h-default font-muoto-regular text-md text-white w-full flex items-center justify-start gap-x-12 px-8 bg-pebble hover:theme-color hover:text-black !ring-0 transition-all disabled:pointer-events-none disabled:hover:bg-pebble disabled:hover:text-black`,
};

const classes = computed(() => VARIANTS[props.variant]);
</script>
