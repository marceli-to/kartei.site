<template>
  <nav
    class="fixed top-12 right-0 z-80 h-[calc(100dvh-0.75rem)] w-full max-w-3xs bg-snow pt-174 dark:bg-graphite"
    :class="{ 'visible opacity-100': isOpen, 'pointer-events-none invisible opacity-0': !isOpen }">
    <ul class="mx-16 border-t border-t-black dark:border-t-white">
      <li>
        <router-link
          :to="{ name: 'archives' }"
          :class="{ 'text-graphite dark:text-black': $route.name === 'archives' }"
          @click="$emit('toggle')">
          Meine Karteien
        </router-link>
      </li>
      <li>
        <router-link
          :to="{ name: 'settings' }"
          :class="{ 'text-graphite dark:text-black': $route.name === 'settings' }"
          @click="$emit('toggle')">
          Einstellungen
        </router-link>
      </li>
      <li>
        <a href="/">Startseite</a>
      </li>
      <li>
        <a href="">Kontakt</a>
      </li>
      <li>
        <a href="">Impressum</a>
      </li>
      <li>
        <a href="">Datenschutz</a>
      </li>
      <li>
        <!-- POST, so another site can't log the user out with a link -->
        <form
          method="post"
          action="/logout">
          <input
            type="hidden"
            name="_token"
            :value="csrfToken" />
          <button
            type="submit"
            class="gap-x-12">
            <Icon
              name="chevron-right"
              variant="small" />
            Abmelden
          </button>
        </form>
      </li>
    </ul>
  </nav>
</template>

<script setup>
import Icon from '@/components/icons/Icon.vue';

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
});

// Define emits to communicate back to parent
defineEmits(['toggle']);
</script>
<style scoped>
@reference "@/../../css/app.css";
nav a,
nav button {
  @apply flex w-full items-center border-b border-b-black py-16 font-muoto-medium leading-none transition-all hover:text-graphite dark:border-b-white dark:hover:text-black;
}
</style>
