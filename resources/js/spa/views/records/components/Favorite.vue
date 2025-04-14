<template>
  <button @click="toggle" 
    :aria-label="isFavorite ? 
    'Favorisiert' : 
    'Favorisieren'">
    <IconHeart v-if="isFavorite" />
    <IconHeart v-else variant="outline" />
  </button>
</template>

<script setup>
import { computed } from 'vue';
import { toggleFavorite } from '@/services/api/favorite';
import IconHeart from '@/components/icons/Heart.vue';
import { useUserStore } from '@/stores/user';

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

const toggle = async () => {
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
  }
};

</script>
