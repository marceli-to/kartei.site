<template>
  <component
    v-if="!isRouterLink"
    :is="tag"
    :type="tag === 'button' ? type : undefined"
    :href="tag === 'a' ? href : undefined"
    :aria-label="label"
    :title="label"
    :disabled="tag === 'button' ? disabled : undefined"
    :class="[baseClasses, defaultBorderClasses, classes]">
    <span :class="contentClasses">
      <component 
        v-if="icon?.name && icon.position === 'left'" 
        :is="resolveIconComponent" 
        :variant="icon.variant || 'small'" 
      />
      <span :class="{ 'flex-1 text-left': icon?.position === 'center' }">
        {{ label }}
      </span>
      <span v-if="icon?.position === 'center'" class="flex-1 flex justify-start">
        <component 
          v-if="icon.name" 
          :is="resolveIconComponent" 
          :variant="icon.variant || 'small'" 
          class="relative -translate-x-1/2" 
        />
      </span>
      <component 
        v-if="icon?.name && (icon.position === 'right' || !icon.position)" 
        :is="resolveIconComponent" 
        :variant="icon.variant || 'small'" 
      />
    </span>
  </component>
  <RouterLink
    v-else
    :to="to"
    :aria-label="label"
    :title="label"
    :class="[baseClasses, defaultBorderClasses, classes]">
    <span :class="contentClasses">
      <component 
        v-if="icon?.name && icon.position === 'left'" 
        :is="resolveIconComponent" 
        :variant="icon.variant || 'small'" 
      />
      <span :class="{ 'flex-1 text-left': icon?.position === 'center' }">
        {{ label }}
      </span>
      <span v-if="icon?.position === 'center'" class="flex-1 flex justify-start">
        <component 
          v-if="icon.name" 
          :is="resolveIconComponent" 
          :variant="icon.variant || 'small'" 
          class="relative -translate-x-1/2" 
        />
      </span>
      <component 
        v-if="icon?.name && (icon.position === 'right' || !icon.position)" 
        :is="resolveIconComponent" 
        :variant="icon.variant || 'small'" 
      />
    </span>
  </RouterLink>
</template>

<script setup>
import { computed } from 'vue';
import { RouterLink } from 'vue-router';
import IconPlus from '@/components/icons/Plus.vue';
import IconChevronRight from '@/components/icons/ChevronRight.vue';

const props = defineProps({
  type: {
    type: String,
    default: 'submit'
  },
  href: {
    type: String,
    default: ''
  },
  to: {
    type: [String, Object],
    default: ''
  },
  label: {
    type: String,
    required: true
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
    }
  },
  classes: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  }
});

const isRouterLink = computed(() => !!props.to);

const tag = computed(() => {
  if (props.href) return 'a';
  return 'button';
});

const resolveIconComponent = computed(() => {
  if (!props.icon?.name) return null;
  
  const icons = {
    plus: IconPlus,
    chevronright: IconChevronRight,
  };
  return icons[props.icon.name.toLowerCase()] || null;
});

const baseClasses = 'min-h-default flex justify-between w-full font-muoto-regular text-md text-black dark:text-white hover:theme-color hover:border-black disabled:opacity-20 disabled:pointer-events-none transition-all';

const defaultBorderClasses = 'border-y border-graphite pr-8';

const contentClasses = computed(() => {
  if (props.icon?.position === 'center') {
    return 'w-full flex items-center justify-center';
  }
  return {
    'w-full flex items-center justify-between gap-8': props.icon?.name && (props.icon.position === 'right' || !props.icon.position),
    'w-full flex items-center gap-8': props.icon?.name && props.icon.position === 'left',
    'w-full flex items-center justify-center': !props.icon?.name
  };
});
</script>