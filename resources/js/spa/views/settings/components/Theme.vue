<template>
  <Slide :pull="true">
    <form 
      @submit.prevent="handleSubmit"
      class="w-full h-full flex flex-col justify-between"
      v-if="!isLoading">

      <div class="flex flex-col gap-y-20">
        <div>asdf</div>
        <div>asdf</div>
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
import { ref } from 'vue';
import { useDialogStore } from '@/components/dialog/stores/dialog';
import { useToastStore } from '@/components/toast/stores/toast';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import Slide from '@/components/slider/Slide.vue';

const dialogStore = useDialogStore();
const toast = useToastStore();

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false
  }
});

const isLoading = ref(false);
const form = ref({
  
});

const handleSubmit = async () => {
  try {
    isLoading.value = true;
    
  } 
  catch (error) {
    toast.show('Fehler beim Speichern.', 'error');
  }
  finally {
    isLoading.value = false;
  }
}

</script>