<template>
  <Slide class="-top-4 pb-36">
    <div class="w-full h-full flex flex-col justify-between">
      <!-- Content -->
      <div>
        <p>
          Mit dem Löschen Ihres Kontos werden alle Ihre Daten und gespeicherten Informationen unwiderruflich entfernt<template v-if="ownedArchivesCount">, auch Ihre {{ ownedArchivesCount === 1 ? 'Kartei' : ownedArchivesCount + ' Karteien' }} mit allen Karten und Bildern</template>.
        </p>
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
import { deleteUser, getUserSubscription, getOwnedArchives } from '@/services/api/user';
import { useDialogStore } from '@/components/dialog/stores/dialog';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import AccountDeleteDialog from '@/views/settings/partials/AccountDeleteDialog.vue';
import Slide from '@/components/slider/Slide.vue';
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
    await deleteUser();
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