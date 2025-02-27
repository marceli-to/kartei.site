<template>
  <div class="relative w-full flex flex-col justify-between px-8 pb-40 h-full -top-4">
    <div>
      <p>Mit dem Löschen Ihres Kontos werden alle Ihre Daten und gespeicherten Informationen unwiderruflich entfernt.</p>
      <p>Ihre Abonnement läuft noch bis zum <strong>{{ new Date().toLocaleDateString() }}</strong></p>
      <p v-if="error" class="text-red-500 mt-4">{{ error }}</p>
    </div>
    <div v-if="isActive">
      <ButtonGroup>
        <ButtonPrimary 
          label="Konto löschen" 
          @click="showDialog"
          class="bg-white text-flame hover:bg-flame hover:text-white border-flame border"
          :disabled="isDeleting" />
      </ButtonGroup>
    </div>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import { deleteUser } from '@/services/api/user';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import AccountDeleteDialog from '@/views/settings/components/AccountDeleteDialog.vue';
import { useDialogStore } from '@/components/dialog/stores/dialog';
import { useToastStore } from '@/components/toast/stores/toast';

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

async function handleDeleteUser() {
  isDeleting.value = true;
  
  try {
    await deleteUser();
    dialogStore.hide();
  } 
  catch (err) {
    console.log(err);
  } 
  finally {
    isDeleting.value = false;
    dialogStore.hide();
    toast.show('Ihr Konto wurde erfolgreich gelöscht. Sie werden in kürze weitergeleitet.', 'success');

    setTimeout(() => {
      window.location.href = '/';
    }, 2000);

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
      // Reset any errors when canceling
      // error.value = null;
    }
  });
}
</script>