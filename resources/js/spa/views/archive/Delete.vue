<template>
  <Slide class="-top-4 pb-36">
    <div class="w-full h-full flex flex-col justify-between">

      <!-- Content -->
      <div>
        <p>Mit dem Löschen dieser Kartei werden alle Ihre Daten und gespeicherten Informationen unwiderruflich entfernt.</p>
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
import { useDialogStore } from '@/stores/dialog';
import { useToastStore } from '@/stores/toast';
import { useUserStore } from '@/stores/user';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import Slide from '@/components/slider/Slide.vue';
import { notifyError } from '@/services/api/error';

const dialogStore = useDialogStore();
const toast = useToastStore();

const route = useRoute();
const router = useRouter();
const uuid = ref(route.params.uuid || null);

const isDeleting = ref(false);

defineProps({
  isActive: {
    type: Boolean,
    default: false
  }
});

async function handleDelete() {
  isDeleting.value = true;

  try {
    await deleteArchive(uuid.value);
  }
  catch (error) {
    notifyError(error, 'Es ist ein Fehler beim Löschen der Kartei aufgetreten.');
    isDeleting.value = false;
    return;
  }

  // Leave right away; the button stays disabled so it can't delete twice (404)
  toast.show('Die Kartei wurde erfolgreich gelöscht.', 'success');
  await useUserStore().fetchPermissions();
  router.push({ name: 'archives' });
}

function showDialog() {
  dialogStore.show({
    title: 'Möchten Sie diese Kartei wirklich löschen?',
    confirmLabel: 'Löschen',
    cancelLabel: 'Abbrechen',
    size: 'medium',
    onConfirm: handleDelete,
    onCancel: () => {}
  });
}
</script>