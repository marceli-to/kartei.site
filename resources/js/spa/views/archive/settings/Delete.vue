<template>
  <Slide class="-top-4 pb-40">
    <div class="w-full h-full flex flex-col justify-between">

      <!-- Content -->
      <div>
        <p>Mit dem Löschen dieser Kartei werden alle Ihre Daten und gespeicherten Informationen unwiderruflich entfernt.</p>
        <p v-if="error" class="text-red-500 mt-4">{{ error }}</p>
      </div>

      <!-- Actions -->
      <div v-if="isActive">
        <ButtonGroup>
          <ButtonPrimary 
            label="Kartei löschen" 
            @click="showDialog"
            :classes="'bg-white dark:bg-black text-flame hover:bg-flame hover:text-white border-flame border'"
            :disabled="isDeleting" />
        </ButtonGroup>
      </div>
    </div>
  </Slide>
</template>
<script setup>
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';

import { deleteArchive } from '@/services/api/archive';
import { useDialogStore } from '@/components/dialog/stores/dialog';
import { useToastStore } from '@/components/toast/stores/toast';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import Slide from '@/components/slider/Slide.vue';

const dialogStore = useDialogStore();
const toast = useToastStore();

const route = useRoute();
const router = useRouter();
const uuid = ref(route.params.uuid || null);

const error = ref(null);
const isDeleting = ref(false);

const redirectDelay = 2000;

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false
  }
});

async function handleDelete() {
  isDeleting.value = true;

  try {
    await deleteArchive(uuid.value);
    dialogStore.hide();
    toast.show('Die Kartei wurde erfolgreich gelöscht. Sie werden in Kürze weitergeleitet.', 'success');

    setTimeout(() => {
      router.push({ name: 'archives' });
    }, redirectDelay);
  }
  catch (err) {
    toast.show('Es ist ein Fehler beim Löschen der Kartei aufgetreten.', 'error');
  } 
  finally {
    isDeleting.value = false;
  }
}

function showDialog() {
  
  dialogStore.show({
    title: 'Möchten Sie diese Kartei wirklich löschen?',
    confirmLabel: 'Löschen',
    cancelLabel: 'Abbrechen',
    size: 'medium',
    onConfirm: () => {
      handleDelete();
    },
    onCancel: () => {
    }
  });
}
</script>