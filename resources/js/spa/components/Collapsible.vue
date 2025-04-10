<template>
  <div
    v-for="(item, index) in items"
    :key="item.uuid"
    class="border-y border-y-graphite">
    <a
      href="javascript:;"
      @click="toggle(index)"
      class="min-h-default flex items-center justify-between">
      <span class="flex font-muoto-medium">
        <span class="w-48">{{ item.number }}.</span>
        <span>{{ item.title }}</span>
      </span>
      <IconChevronDown :class="isOpen[index] ? 'rotate-180' : ''" class="transition-transform" />
    </a>

    <div v-if="isOpen[index]" class="border-t border-t-graphite">
      <div
        v-for="subItem in item.registers"
        :key="subItem.uuid"
        class="min-h-default flex items-center border-b border-b-graphite last:border-b-0">
        <span class="flex">
          <span class="w-48">{{ subItem.number }}</span>
          <span>{{ subItem.title }}</span>
        </span>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import IconChevronDown from '@/components/icons/ChevronDown.vue';

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
