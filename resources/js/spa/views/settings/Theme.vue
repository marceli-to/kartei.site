<template>
  <Slide :pull="true">
    <div 
      class="w-full h-full flex flex-col justify-between"
      v-if="!isLoading">

      <!-- Form elements -->
      <div class="flex flex-col gap-y-20">
        <InputGroup>
          <InputLabel label="Hintergrund" />
          <InputSelectButtons
            v-model="theme.color_scheme"
            name="color_scheme"
            wrapperClasses="w-full flex gap-x-8"
            labelClasses="w-full"
            :options="colorSchemes"
            @update:modelValue="saveTheme" />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Auszeichnungsfarbe" />
          <InputSelectButtons
            v-model="theme.color_theme"
            name="color_theme"
            wrapperClasses="w-full grid grid-cols-2 gap-8"
            :options="colorThemes"
            @update:modelValue="saveTheme" />
        </InputGroup>
      </div>
    </div>
  </Slide>
</template>
<script setup>
import { onMounted } from 'vue';
import { useTheme } from '@/composables/useTheme';
import InputGroup from '@/components/forms/Group.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputSelectButtons from '@/components/forms/SelectButtons.vue';
import Slide from '@/components/slider/Slide.vue';

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false
  }
});

// Use the theme composable
const { 
  theme, 
  isLoading, 
  colorSchemes, 
  colorThemes, 
  fetchTheme, 
  saveTheme 
} = useTheme();

onMounted(() => {
  fetchTheme();
});
</script>