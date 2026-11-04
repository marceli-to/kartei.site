<template>
  <button
    type="button"
    @click="toggle"
    aria-label="Favorit"
    :aria-pressed="isFavorite"
    :aria-busy="isSaving">
    <Icon name="heart" v-if="isFavorite" />
    <Icon name="heart" v-else variant="outline" />
  </button>
</template>

<script setup>
import { computed, ref } from 'vue';
import { notifyError } from '@/services/api/error';
import { toggleFavorite } from '@/services/api/favorite';
import { useUserStore } from '@/stores/user';
import Icon from '@/components/icons/Icon.vue';

const props = defineProps({
  uuid: {
    type: String,
    required: true
  }
});

const userStore = useUserStore();

const isFavorite = computed(() => {
  return userStore.favorites.includes(props.uuid);
});

// The API toggles, so a second click before the first answer would undo it
const isSaving = ref(false);

const toggle = async () => {
  if (isSaving.value) return;
  isSaving.value = true;

  const currentlyFavorite = isFavorite.value;

  // Optimistically update store
  if (currentlyFavorite) {
    userStore.favorites = userStore.favorites.filter(id => id !== props.uuid);
  } 
  else {
    userStore.favorites.push(props.uuid);
  }

  try {
    await toggleFavorite(props.uuid);
  } 
  catch (error) {
    if (currentlyFavorite) {
      userStore.favorites.push(props.uuid);
    } 
    else {
      userStore.favorites = userStore.favorites.filter(id => id !== props.uuid);
    }
    notifyError(error, 'Der Favorit konnte nicht gespeichert werden.');
  }
  finally {
    isSaving.value = false;
  }
};

</script>
