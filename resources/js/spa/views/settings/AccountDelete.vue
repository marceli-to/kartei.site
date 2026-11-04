<template>
  <Slide class="-top-4 pb-36">
    <div class="w-full h-full flex flex-col justify-between">
      <!-- Content -->
      <div>
        <p>Mit dem Löschen Ihres Kontos werden alle Ihre Daten und gespeicherten Informationen unwiderruflich entfernt.</p>
        <p v-if="subscriptionEndsAt">Ihr Abonnement läuft noch bis zum <strong>{{ subscriptionEndsAt }}</strong>.</p>
      </div>

      <!-- Actions -->
      <div v-if="isActive">
        <ButtonGroup>
          <ButtonPrimary 
            label="Konto löschen" 
            @click="showDialog"
            :classes="'bg-white dark:bg-black text-flame hover:bg-flame hover:text-white border-flame border'"
            :disabled="isDeleting" />
        </ButtonGroup>
      </div>
    </div>
  </Slide>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import { deleteUser, getUserSubscription } from '@/services/api/user';
import { useDialogStore } from '@/components/dialog/stores/dialog';
import { useToastStore } from '@/components/toast/stores/toast';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import Slide from '@/components/slider/Slide.vue';

const dialogStore = useDialogStore();
const toast = useToastStore();

const isDeleting = ref(false);
const subscriptionEndsAt = ref(null);

defineProps({
  isActive: {
    type: Boolean,
    default: false
  }
});

const redirectAfterDeletion = '/auf-wiedersehen';

onMounted(async () => {
  try {
    const subscription = await getUserSubscription();
    const endsAt = subscription?.data?.ends_at;
    if (endsAt) subscriptionEndsAt.value = new Date(endsAt).toLocaleDateString('de-CH');
  }
  catch (error) {
    console.error(error);
  }
});

async function handleDelete() {
  isDeleting.value = true;

  try {
    await deleteUser();
  }
  catch (error) {
    console.error(error);
    toast.show('Es ist ein Fehler beim Löschen Ihres Kontos aufgetreten.', 'error');
    isDeleting.value = false;
    return;
  }

  // The session is gone: leave the SPA right away (isDeleting stays true until then)
  window.location.href = redirectAfterDeletion;
}

function showDialog() {
  dialogStore.show({
    title: 'Möchten Sie Ihr Konto wirklich löschen?',
    message: 'Diese Aktion kann nicht rückgängig gemacht werden.',
    confirmLabel: 'Löschen',
    cancelLabel: 'Abbrechen',
    size: 'medium',
    onConfirm: handleDelete,
    onCancel: () => {}
  });
}
</script>