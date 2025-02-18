<template>
  <div class="flex flex-grow w-full overflow-hidden mt-64 relative gap-x-8">

    <!-- Navbar -->
    <nav class="w-2/12 bg-gray-300 sticky left-0 mt-48 min-h-full z-50">

      <!-- Name of previous section -->
      <template v-if="activeIndex > 0">
        <div class="absolute left-0 -top-48 w-full flex items-center justify-between bg-white">
          <h1 class="opacity-20">
            {{ previousSectionName }}
          </h1>
          <a 
            href="javascript:;"
            @click.prevent="prev"
            title="Prev Section"
            class="opacity-20">
            <IconChevronLeft variant="small" />
          </a>
        </div>
      </template>

      <ul>
        <li v-for="(item, index) in sections" :key="index" 
            @click="activeIndex = index"
            class="cursor-pointer p-2" 
            :class="{'font-muoto-medium': activeIndex === index}">
          {{ item.name }}
        </li>
      </ul>
    </nav>
    
    <!-- Content Area -->
    <div 
      class="flex flex-grow transition-transform duration-300 w-full relative z-40" 
      :style="{ transform: `translateX(-${computedOffsets[activeIndex]}%)` }">
      <section 
        v-for="(item, index) in sections" 
        :key="index" 
        :class="item.class"
        class="flex-shrink-0">
        <h1 
          class="ml-8 opacity-1 flex justify-between items-center"
          :class="{
            'transition-none delay-300': activeIndex === index, 
            'opacity-20': activeIndex !== index,
            'opacity-0 transition-all duration-none': activeIndex > index
          }">
          <span>{{ item.name }}</span>
          <template v-if="index >= activeIndex && index < sections.length - 1">
            <a 
              href="javascript:;"
              @click.prevent="next"
              title="Next Section"
              class="mr-8">
              <IconChevronRight variant="small" />
            </a>
          </template>
        </h1>
        <div class="flex flex-grow min-h-full px-8 pt-22">
          <div 
            class="flex-grow bg-gray-200 min-h-full"
            :class="{ 'opacity-20 transition-all duration-none': activeIndex !== index }">
            test
          </div>
        </div>
      </section>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import IconChevronRight from '@/components/icons/ChevronRight.vue';
import IconChevronLeft from '@/components/icons/ChevronLeft.vue';

// State
const activeIndex = ref(0)
const sections = ref([
  { name: "Profile", width: 25, class: "w-3/12" },
  { name: "Adresse", width: 41.6667, class: "w-5/12" },
  { name: "Abonnement", width: 41.66667, class: "w-5/12" },
  { name: "Log", width: 41.66667, class: "w-5/12" }
])

// Computed
const computedOffsets = computed(() => {
  const offsets = []
  let sum = 0
  sections.value.forEach((section) => {
    offsets.push(sum)
    sum += section.width
  })
  return offsets
})

// New computed property for previous section name
const previousSectionName = computed(() => {
  return activeIndex.value > 0 ? sections.value[activeIndex.value - 1].name : ''
})

function next() {
  if (activeIndex.value < sections.value.length - 1) {
    activeIndex.value++
  }
}

function prev() {
  if (activeIndex.value > 0) {
    activeIndex.value--
  }
}
</script>