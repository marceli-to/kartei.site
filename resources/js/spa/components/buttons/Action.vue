<template>
  <component
    :is="element"
    v-bind="elementAttrs"
    :aria-label="label"
    :title="label"
    :class="[baseClasses, defaultBorderClasses, classes]">
    <span :class="contentClasses">
      <Icon
        v-if="iconName && icon.position === 'left'"
        :name="iconName"
        :variant="icon.variant || 'small'" />
      <span :class="{ 'flex-1 text-left': icon?.position === 'center' }">
        {{ label }}
      </span>
      <span
        v-if="icon?.position === 'center'"
        class="flex flex-1 justify-start">
        <Icon
          v-if="iconName"
          :name="iconName"
          :variant="icon.variant || 'small'"
          class="relative -translate-x-1/2" />
      </span>
      <Icon
        v-if="iconName && (icon.position === 'right' || !icon.position)"
        :name="iconName"
        :variant="icon.variant || 'small'" />
    </span>
  </component>
</template>

<script setup>
import { computed } from 'vue';
import { RouterLink } from 'vue-router';
import Icon from '@/components/icons/Icon.vue';

const props = defineProps({
  type: {
    type: String,
    default: 'submit',
  },
  href: {
    type: String,
    default: '',
  },
  to: {
    type: [String, Object],
    default: '',
  },
  label: {
    type: String,
    required: true,
  },
  icon: {
    type: Object,
    default: () => null,
    validator: (value) => {
      if (!value) return true;
      const { name, variant, position } = value;
      return (
        name &&
        (!variant || ['tiny', 'small', 'small-bold', 'large'].includes(variant)) &&
        (!position || ['left', 'right', 'center'].includes(position))
      );
    },
  },
  classes: {
    type: String,
    default: '',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  variant: {
    type: String,
    default: 'default',
    validator: (value) => ['default', 'box'].includes(value),
  },
});

// A router link (to), a plain link (href) or a button
const element = computed(() => {
  if (props.to) return RouterLink;
  if (props.href) return 'a';
  return 'button';
});

const elementAttrs = computed(() => {
  if (props.to) return { to: props.to };
  if (props.href) return { href: props.href };
  return { type: props.type, disabled: props.disabled };
});

// icon.name as written by the callers ('Plus', 'ChevronRight') -> registry name
const iconName = computed(() =>
  props.icon?.name ? props.icon.name.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase() : null,
);

const baseClasses =
  'min-h-default flex items-center justify-between w-full font-muoto-regular text-md text-black dark:text-white hover:theme-color hover:border-black disabled:opacity-20 disabled:pointer-events-none transition-all';

const defaultBorderClasses = computed(() => {
  return props.variant === 'box' ? 'border border-graphite px-8' : 'border-y border-graphite pr-8';
});

const contentClasses = computed(() => {
  if (props.icon?.position === 'center') {
    return 'w-full flex items-center justify-center';
  }
  return {
    'w-full flex items-center justify-between gap-8':
      props.icon?.name && (props.icon.position === 'right' || !props.icon.position),
    'w-full flex items-center gap-8': props.icon?.name && props.icon.position === 'left',
    'w-full flex items-center justify-center': !props.icon?.name,
  };
});
</script>
