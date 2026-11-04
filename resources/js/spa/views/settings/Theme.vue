<template>
  <Slide :pull="true">
    <div
      v-if="!isLoading"
      class="flex h-full w-full flex-col justify-between">
      <!-- Form elements -->
      <div class="flex flex-col gap-y-20">
        <InputGroup>
          <InputLabel label="Hintergrund" />
          <InputSelectButtons
            v-model="theme.color_scheme"
            legend="Hintergrund"
            name="color_scheme"
            wrapper-classes="w-full flex gap-x-8"
            label-classes="w-full"
            :options="colorSchemes"
            @update:model-value="saveTheme" />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Auszeichnungsfarbe" />
          <InputSelectButtons
            v-model="theme.color_theme"
            legend="Auszeichnungsfarbe"
            name="color_theme"
            wrapper-classes="w-full grid grid-cols-2 gap-8"
            :options="colorThemes"
            @update:model-value="saveTheme" />
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

defineProps({
  isActive: {
    type: Boolean,
    default: false,
  },
});

// Use the theme composable
const { theme, isLoading, colorSchemes, colorThemes, fetchTheme, saveTheme } = useTheme();

onMounted(() => {
  fetchTheme();
});
</script>
