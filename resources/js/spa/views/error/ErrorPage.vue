<template>
  <div class="flex justify-center items-center text-center font-otto-bold text-lg text-flame">
    <div>
      <h1 class="text-huge font-otto-regular">{{ status }}</h1>
      <p class="text-center">
        {{ page.message }}<br>
        <template v-if="status === 401">
          Sie werden in {{ countdown }} Sekunde<span v-if="countdown !== 1">n</span> weitergeleitet.
        </template>
        <template v-else>
          Keine Sorge, hier geht es zurück:
          <span class="flex justify-center mt-16">
            <router-link :to="{ name: 'archives' }" aria-label="Zurück zu Meine Karteien">
              <IconChevronLeft variant="large-rounded" />
            </router-link>
          </span>
        </template>
      </p>
    </div>
  </div>
</template>
<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { usePageTitle } from '@/composables/usePageTitle';
import IconChevronLeft from '@/components/icons/ChevronLeft.vue';

// One page for /error/401, /error/403 and /error/404 (status comes from the route)
const props = defineProps({
  status: {
    type: Number,
    required: true
  }
});

const PAGES = {
  // Reached when an API request gets a 401 (session expired); the login
  // page sends the user back to /archiv afterwards
  401: { message: 'Sitzung abgelaufen' },
  403: { message: 'Autorisation fehlgeschlagen' },
  404: { message: 'Seite nicht gefunden' },
};
const page = computed(() => PAGES[props.status] ?? PAGES[404]);

const { setTitle } = usePageTitle();
setTitle('');

const countdown = ref(5);
let interval = null;

onMounted(() => {
  if (props.status !== 401) return;
  interval = setInterval(() => {
    countdown.value--;
    if (countdown.value === 0) {
      clearInterval(interval);
      document.location.href = '/login';
    }
  }, 1000);
});

onUnmounted(() => clearInterval(interval));
</script>
