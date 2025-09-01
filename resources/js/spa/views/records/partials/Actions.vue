<template>
  <div class="flex items-center absolute w-full h-106">
    <div class="w-2/12 min-w-nav shrink-0">
      <slot />
    </div>
    <nav 
      class="w-10/12 min-w-10/12 ml-8 relative" 
      v-if="links">
      <ul class="flex justify-start gap-x-44 2xl:gap-x-16 leading-none">
        <li 
          v-for="(item, idx) in links" 
          :key="idx" 
          class="shrink-0 2xl:w-[calc((25%/2)_-_14px)]">
          <component
            v-if="item.type === 'link'"
            :is="item.router ? 'router-link' : 'a'"
            v-bind="item.router ? { to: item.to } : { href: item.to, target: item.target || '_self' }"
            class="flex items-center gap-x-12"
            :class="item.class"
          >
            <component :is="item.icon" v-if="item.icon" :variant="item.iconVariant || 'square'" />
            <span class="text-xs font-muoto-medium">{{ item.label }}</span>
          </component>
          <button
            v-else-if="item.type === 'button'"
            type="button"
            class="flex items-center gap-x-12"
            :class="item.class"
            :disabled="item.disabled"
            @click="item.onClick"
          >
            <component :is="item.icon" v-if="item.icon" :variant="item.iconVariant || 'square'" />
            <span class="text-xs font-muoto-medium">{{ item.label }}</span>
          </button>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script setup>
import { defineProps } from 'vue'

const props = defineProps({
  links: {
    type: Array,
    required: false,
  },
})
</script>
