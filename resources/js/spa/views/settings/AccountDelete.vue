<template>
  <Slide class="-top-4 pb-40">
    <div class="w-full h-full flex flex-col justify-between">
      <!-- Content -->
      <div>
        <p>Mit dem Löschen Ihres Kontos werden alle Ihre Daten und gespeicherten Informationen unwiderruflich entfernt.</p>
        <p>Ihre Abonnement läuft noch bis zum <strong>{{ new Date().toLocaleDateString() }}</strong></p>
        <p v-if="error" class="text-red-500 mt-4">{{ error }}</p>
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
import { ref } from 'vue';
import { deleteUser } from '@/services/api/user';
import { useDialogStore } from '@/components/dialog/stores/dialog';
import { useToastStore } from '@/components/toast/stores/toast';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import AccountDeleteDialog from '@/views/settings/partials/AccountDeleteDialog.vue';
import Slide from '@/components/slider/Slide.vue';

const dialogStore = useDialogStore();
const toast = useToastStore();

const error = ref(null);
const isDeleting = ref(false);

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false
  }
});

const redirectAfterDeletion = '/auf-wiedersehen';
const redirectDelay = 2000;

async function handleDeleteUser() {
  isDeleting.value = true;
  
  try {
    await deleteUser();
    dialogStore.hide();
    toast.show('Ihr Konto wurde erfolgreich gelöscht. Sie werden in ein paar Sekunden weitergeleitet.', 'success');
    
    setTimeout(() => {
      window.location.href = redirectAfterDeletion;
    }, redirectDelay);
  } 
  catch (err) {
    console.log(err);
    toast.show('Es ist ein Fehler beim Löschen Ihres Kontos aufgetreten.', 'error');
  } 
  finally {
    isDeleting.value = false;
  }
}

function showDialog() {
  
  dialogStore.show({
    title: 'Möchten Sie ihr Konto wirklich löschen?',
    component: AccountDeleteDialog,
    confirmLabel: 'Löschen',
    cancelLabel: 'Abbrechen',
    size: 'medium',
    onConfirm: () => {
      handleDeleteUser();
    },
    onCancel: () => {
    }
  });
}
</script>