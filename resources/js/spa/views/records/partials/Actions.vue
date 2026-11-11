<template>
  <div class="absolute flex h-106 w-full items-center">
    <div class="w-2/12 min-w-nav shrink-0">
      <slot />
    </div>
    <nav
      v-if="links.length"
      class="relative ml-8 w-10/12 min-w-10/12">
      <ul class="flex justify-start gap-x-44 leading-none 2xl:gap-x-16">
        <li
          v-for="(item, idx) in links"
          :key="idx"
          class="shrink-0 2xl:w-[calc(12.5%-14px)]">
          <component
            :is="item.router ? 'router-link' : 'a'"
            v-if="item.type === 'link'"
            v-bind="item.router ? { to: item.to } : { href: item.to, target: item.target || '_self' }"
            class="flex items-center gap-x-12"
            :class="item.class">
            <Icon
              v-if="item.icon"
              :name="item.icon"
              :variant="item.iconVariant || 'square'" />
            <span class="font-muoto-medium text-xs">{{ item.label }}</span>
          </component>
          <button
            v-else-if="item.type === 'button'"
            type="button"
            class="flex items-center gap-x-12 disabled:cursor-default disabled:opacity-20"
            :class="[item.active === false ? 'text-graphite hover:text-black dark:hover:text-white' : '', item.class]"
            :aria-pressed="item.active ?? undefined"
            :disabled="item.disabled"
            @click="item.onClick">
            <Icon
              v-if="item.icon"
              :name="item.icon"
              :variant="item.iconVariant || 'square'" />
            <span class="font-muoto-medium text-xs">{{ item.label }}</span>
          </button>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script setup>
import Icon from '@/components/icons/Icon.vue';

// [{ type: 'link' | 'button', label, icon, iconVariant?, to?, router?, onClick?, disabled?, active? }]
// active: false = an unselected view toggle (graphite), true = the selected one
defineProps({
  links: {
    type: Array,
    default: () => [],
  },
});
</script>
