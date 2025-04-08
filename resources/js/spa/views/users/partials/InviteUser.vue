<template>
  <div class="w-full h-full flex flex-col justify-between pb-16">
    <div>
      <ButtonAuth label="Einladungslink versenden" @click="send" :disabled="isSending" />
      <p class="text-sm p-8 mt-8">Mit Abschicken des Einladungslinks erhält {{ user.firstname }} {{ user.name }} Zugang zu den ausgewählten Karteien. Zugriffsrechte können innerhalb der Kartei in den Voreinstellungen angepasst werden.</p>
    </div>
    <ButtonGroup>
      <ButtonPrimary 
        @click="$emit('cancel')" 
        label="Abbrechen" />
    </ButtonGroup>
  </div>
</template>
<script setup>
import { ref, onMounted, inject } from 'vue';
import { useToastStore } from '@/components/toast/stores/toast';
import { sendInvitation } from '@/services/api/user';
import ButtonAuth from '@/components/buttons/Auth.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';

const toast = useToastStore();
const archivePermissionsMap = inject('archivePermissionsMap', {});

const props = defineProps({
  user: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['success', 'cancel']);
const isSending = ref(false);

const send = async () => {
  try {
    isSending.value = true;
    // Get archive IDs from the permissions map
    const archiveIds = Object.keys(archivePermissionsMap);
    
    // Send invitation with only the archives we've assigned permissions to
    await sendInvitation(props.user, archiveIds);
    toast.show('Einladungslink wurde versendet.', 'success');
    emit('success');
  } 
  catch (error) {
    toast.show('Fehler beim Versand des Einladungslinks.', 'error');
    console.error(error);
  } 
  finally {
    isSending.value = false;
  }
};
</script>