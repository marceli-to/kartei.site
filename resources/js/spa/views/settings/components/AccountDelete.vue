<template>
  <div class="relative w-full flex flex-col justify-between px-8 pb-40 h-full -top-4">
    <div>
      <p>Mit dem Löschen Ihres Kontos werden alle Ihre Daten und gespeicherten Informationen unwiderruflich entfernt.</p>
      <p>Ihre Abonnement läuft noch bis zum <strong>{{ new Date().toLocaleDateString() }}</strong></p>
    </div>
    <div v-if="isActive">
      <ButtonGroup>
        <ButtonPrimary 
          label="Konto löschen" 
          @click="showDialog"
          class="bg-white text-flame hover:bg-flame hover:text-white border-flame border" />
      </ButtonGroup>
    </div>
  </div>
</template>
<script setup>
import { ref } from 'vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import AccountDeleteDialog from '@/views/settings/components/AccountDeleteDialog.vue';
import { useDialogStore } from '@/components/dialog/stores/dialog';
const dialogStore = useDialogStore();

const props = defineProps({
  isActive: {
    type: Boolean,
    default: false
  }
});

function showDialog() {
  dialogStore.show({
    title: 'Möchten Sie ihr Konto wirklich löschen?',
    component: AccountDeleteDialog,
    confirmLabel: 'Löschen',
    cancelLabel: 'Abbrechen',
    size: 'medium',
    onConfirm: () => {
      // console.log('Save clicked');
    },
    onCancel: () => {
    }
  });
}

</script>