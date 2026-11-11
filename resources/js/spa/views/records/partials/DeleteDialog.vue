<template>
  <!--
    Shown in the dialog (size "full", no cross) before a record is deleted (KAR0001_Desktop_261111, p. 11):
    the question, the first image in a square frame and the record's fields in the tile style,
    a 380px column in the middle; Abbrechen / Löschen at the bottom.
  -->
  <div class="flex flex-1 flex-col items-center gap-y-36">
    <h3
      id="dialog-title"
      class="-mt-4 text-center font-muoto-medium">
      Möchten Sie die Karte {{ record.display_number }} wirklich löschen?
    </h3>

    <div class="flex w-full max-w-380 flex-1 flex-col gap-y-28">
      <ImageCard v-if="image">
        <Image
          :src="image.url"
          :alt="image.alt || ''"
          :width="image.width || undefined"
          :height="image.height || undefined"
          spacing="m-40" />
      </ImageCard>

      <div class="border-y border-y-graphite">
        <div class="flex min-h-default items-center justify-between font-muoto-medium">
          {{ record.display_number }}
          <Icon
            name="heart"
            :variant="isFavorite ? undefined : 'outline'" />
        </div>
        <div
          v-for="field in record.fields || []"
          :key="field.uuid"
          class="min-h-default border-t border-t-graphite py-3">
          {{ field.content }}
        </div>
      </div>

      <ButtonGroup class="mt-auto mb-20 pt-40">
        <AppButton
          label="Abbrechen"
          type="button"
          :disabled="isBusy"
          @click="dialogStore.hide()" />
        <AppButton
          label="Löschen"
          type="button"
          :loading="isBusy"
          @click="confirm" />
      </ButtonGroup>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { storeToRefs } from 'pinia';
import { useDialogStore } from '@/stores/dialog';
import { useToastStore } from '@/stores/toast';
import { useUserStore } from '@/stores/user';
import { notifyError } from '@/services/api/error';
import ImageCard from '@/components/media/Card.vue';
import Image from '@/components/media/Image.vue';
import Icon from '@/components/icons/Icon.vue';
import ButtonGroup from '@/components/buttons/Group.vue';
import AppButton from '@/components/buttons/Button.vue';

const props = defineProps({
  record: {
    type: Object,
    required: true,
  },
  // Deletes the record (a promise); then() runs after a successful delete
  action: {
    type: Function,
    required: true,
  },
  then: {
    type: Function,
    default: null,
  },
});

const dialogStore = useDialogStore();
const { isBusy } = storeToRefs(dialogStore);
const userStore = useUserStore();

const image = computed(() => props.record.media?.[0] || null);
const isFavorite = computed(() => userStore.favorites.includes(props.record.uuid));

// Stays open (busy) while deleting; on failure it stays open with the error toast
const confirm = async () => {
  if (isBusy.value) return;
  isBusy.value = true;
  try {
    await props.action();
  } catch (error) {
    isBusy.value = false;
    notifyError(error, 'Fehler beim Löschen der Karte.');
    return;
  }
  useToastStore().show('Karte erfolgreich gelöscht', 'success');
  dialogStore.hide();
  await props.then?.();
};
</script>
