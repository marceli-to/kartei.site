<template>
  <nav
    class="fixed top-12 right-0 z-80 bg-snow dark:bg-graphite pt-174 w-full max-w-[16rem] h-[calc(100dvh_-_0.75rem)]"
    :class="{ 'opacity-100 visible': isOpen, 'opacity-0 invisible pointer-events-none': !isOpen }">
    <ul class="mx-16 border-t border-t-black dark:border-t-white">
      <li>
        <router-link
          :to="{ name: 'archives' }"
          @click="$emit('toggle')"
          :class="{ 'text-graphite dark:text-black': $route.name === 'archives' }">
          Meine Karteien
        </router-link>
      </li>
      <li>
        <router-link
          :to="{ name: 'settings' }"
          @click="$emit('toggle')"
          :class="{ 'text-graphite dark:text-black': $route.name === 'settings' }">
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
        <form method="post" action="/logout">
          <input type="hidden" name="_token" :value="csrfToken">
          <button type="submit" class="gap-x-12">
            <Icon name="chevron-right" variant="small" />
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
    default: false
  }
});

// Define emits to communicate back to parent
defineEmits(['toggle']);
</script>
<style scoped>
nav a,
nav button {
  @apply font-muoto-medium leading-none w-full py-16 border-b border-b-black dark:border-b-white flex items-center hover:text-graphite dark:hover:text-black transition-all;
}
</style>