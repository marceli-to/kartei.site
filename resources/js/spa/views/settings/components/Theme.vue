<template>
  <Slide :pull="true">
    <form 
      @submit.prevent="handleSubmit"
      class="w-full h-full flex flex-col justify-between"
      v-if="!isLoading">

      <!-- Form elements -->
      <div class="flex flex-col gap-y-20">
        <InputGroup>
          <InputLabel label="Hintergrund" />
          <InputSelectButtons
            v-model="form.color_scheme"
            name="color_scheme"
            wrapperClasses="w-full flex gap-x-8"
            labelClasses="w-full"
            :options="color_schemes" />
        </InputGroup>
        <InputGroup>
          <InputLabel label="Auszeichnungsfarbe" />
          <InputSelectButtons
            v-model="form.color_theme"
            name="color_theme"
            wrapperClasses="w-full grid grid-cols-2 gap-8"
            :valueActiveClassMap="{
              'ice': 'bg-ice border-black !text-black',
              'candy': 'bg-candy border-black !text-black',
              'lemon': 'bg-lemon border-black !text-black',
              'lime': 'bg-lime border-black !text-black'
            }"
            :options="color_themes" />
        </InputGroup>
      </div>

      <!-- Actions -->
      <div v-if="isActive">
        <ButtonGroup>
          <ButtonPrimary label="Speichern" />
        </ButtonGroup>
      </div>

    </form>
  </Slide>
</template>
<script setup>
import { ref, onMounted, watch } from 'vue';
import { useToastStore } from '@/components/toast/stores/toast';
import { getUserTheme, updateUserTheme } from '@/services/api/user';
import InputGroup from '@/components/forms/Group.vue';
import InputLabel from '@/components/forms/Label.vue';
import InputSelectButtons from '@/components/forms/SelectButtons.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import Slide from '@/components/slider/Slide.vue';

const toast = useToastStore();

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false
  }
});

const color_schemes = [
  { label: 'Hell', value: 'light' },
  { label: 'Dunkel', value: 'dark' },
];

const color_themes = [
  { label: 'Blau', value: 'ice' },
  { label: 'Pink', value: 'candy' },
  { label: 'Gelb', value: 'lemon' },
  { label: 'Grün', value: 'lime' }
];

const isLoading = ref(false);
const isSaving = ref(false);

const form = ref({
  color_scheme: '',
  color_theme: ''
});

onMounted(async () => {
  try {
    isLoading.value = true;
    const userThemeData = await getUserTheme();
    if (userThemeData.data) {
      form.value.color_scheme = userThemeData.data.color_scheme;
      form.value.color_theme = userThemeData.data.color_theme;
    }
  } 
  catch (error) {
    console.error(error);
  } 
  finally {
    isLoading.value = false;
  }
});

const handleSubmit = async () => {
  try {
    isSaving.value = true;
    await updateUserTheme(form.value);
    toast.show('Einstellungen geändert.', 'success');
  } 
  catch (error) {
    toast.show('Fehler beim Speichern.', 'error');
  }
  finally {
    isSaving.value = false;
  }
}

watch(() => form.value.color_scheme, (newColorScheme, oldColorScheme) => {
  updateTheme(newColorScheme);
});

const updateTheme = (scheme) => {
  
  if (scheme === 'dark') {
    document.documentElement.classList.add('dark');
    document.documentElement.classList.remove('light');
  }
  else {
    document.documentElement.classList.add('light');
    document.documentElement.classList.remove('dark');
  }
};
</script>