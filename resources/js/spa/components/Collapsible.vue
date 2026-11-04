<template>
  <div
    v-for="(item, index) in items"
    :key="item.uuid"
    class="border-b border-b-graphite first:border-t first:border-t-graphite">
    <!-- Only categories with registers open; the others are plain rows -->
    <component
      :is="item.registers.length > 0 ? 'button' : 'div'"
      :type="item.registers.length > 0 ? 'button' : undefined"
      :aria-expanded="item.registers.length > 0 ? !!isOpen[index] : undefined"
      @click="toggle(index)"
      class="w-full text-left min-h-default flex items-center justify-between">
      <span class="flex font-muoto-medium">
        <span class="w-48">{{ item.number }}.</span>
        <span>{{ item.name }}</span>
      </span>
      <Icon name="chevron-down" 
        :class="isOpen[index] ? 'rotate-180' : ''" 
        class="transition-transform"
        v-if="item.registers.length > 0" />
    </component>

    <div 
      class="border-t border-t-graphite"
      v-if="isOpen[index] && item.registers.length > 0">
      <div
        v-for="subItem in item.registers"
        :key="subItem.uuid"
        class="min-h-default flex items-center border-b border-b-graphite last:border-b-0">
        <span class="flex">
          <span class="w-48">{{ subItem.number }}</span>
          <span>{{ subItem.name }}</span>
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import Icon from '@/components/icons/Icon.vue';

const props = defineProps({
  items: {
    type: Array,
    required: true
  }
})

const isOpen = ref([false])

const toggle = (index) => {
  isOpen.value[index] = !isOpen.value[index]
}

watch(() => props.items, (newItems) => {
  isOpen.value = newItems.map(() => false)
}, { immediate: true })
</script>
