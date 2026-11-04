<template>
  <DeleteSlide label="Konto löschen" :isActive="isActive" :disabled="isDeleting" @delete="showDialog">
    <p>
      Mit dem Löschen Ihres Kontos werden alle Ihre Daten und gespeicherten Informationen unwiderruflich entfernt<template v-if="ownedArchivesCount">, auch Ihre {{ ownedArchivesCount === 1 ? 'Kartei' : ownedArchivesCount + ' Karteien' }} mit allen Karten und Bildern</template>.
    </p>
    <p v-if="subscriptionEndsAt">Ihr Abonnement läuft noch bis zum <strong>{{ subscriptionEndsAt }}</strong>.</p>
  </DeleteSlide>
</template>
<script setup>
import { ref, onMounted } from 'vue';
import { deleteAccount, getOwnedArchives } from '@/services/api/user';
import { getUserSubscription } from '@/services/api/subscription';
import { useDialogStore } from '@/stores/dialog';
import AccountDeleteDialog from '@/views/settings/partials/AccountDeleteDialog.vue';
import DeleteSlide from '@/components/slider/DeleteSlide.vue';
import { notifyError } from '@/services/api/error';

const dialogStore = useDialogStore();

const isDeleting = ref(false);
const subscriptionEndsAt = ref(null);
const ownedArchivesCount = ref(0);

defineProps({
  isActive: {
    type: Boolean,
    default: false
  }
});

const redirectAfterDeletion = '/auf-wiedersehen';

onMounted(async () => {
  try {
    ownedArchivesCount.value = (await getOwnedArchives()).data.length;
  }
  catch (error) {
    notifyError(error, 'Die Karteien konnten nicht geladen werden.');
  }

  try {
    const subscription = await getUserSubscription();
    const endsAt = subscription?.data?.ends_at;
    if (endsAt) subscriptionEndsAt.value = new Date(endsAt).toLocaleDateString('de-CH');
  }
  catch {
    // Only the note about the subscription end is missing then
  }
});

async function handleDelete() {
  isDeleting.value = true;

  try {
    await deleteAccount();
  }
  catch (error) {
    notifyError(error, 'Es ist ein Fehler beim Löschen Ihres Kontos aufgetreten.');
    isDeleting.value = false;
    return;
  }

  // The session is gone: leave the SPA right away (isDeleting stays true until then)
  window.location.href = redirectAfterDeletion;
}

// Loaded when the dialog opens, so the list is current (archives may have changed meanwhile)
async function showDialog() {
  let archives = [];
  try {
    archives = (await getOwnedArchives()).data;
  }
  catch (error) {
    notifyError(error, 'Die Karteien konnten nicht geladen werden.');
    return;
  }

  dialogStore.show({
    title: 'Möchten Sie Ihr Konto wirklich löschen?',
    component: AccountDeleteDialog,
    props: { archives },
    confirmLabel: 'Löschen',
    cancelLabel: 'Abbrechen',
    size: 'medium',
    onConfirm: handleDelete,
    onCancel: () => {}
  });
}
</script>