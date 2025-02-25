<template>
  <nav
    class="fixed top-12 right-0 3xl:right-[calc((100%_-_100rem)/2)] z-80 bg-snow pt-174 w-full max-w-[16rem] h-[calc(100dvh_-_0.75rem)] transform transition-transform duration-300 ease-in-out"
    :class="{ 'opacity-100': isOpen, 'opacity-0': !isOpen }">
    <ul class="mx-16 border-t border-t-black">
      <li>
        <a href="">Meine Karteien</a>
      </li>
      <li>
        <a href="">Einstellungen</a>
      </li>
      <li>
        <a href="" class="text-graphite">Startseite</a>
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
        <a 
          href="/logout" 
          class="gap-x-12"
          title="Abmelden">
          <IconChevronRight variant="small" />
          Abmelden
        </a>
      </li>
    </ul>
  </nav>
</template>

<script setup>
import { ref } from 'vue';
import IconChevronRight from '@/components/icons/ChevronRight.vue';
const isOpen = ref(false);
const toggle = () => {
  isOpen.value = !isOpen.value;
};
defineExpose({ toggle, isOpen });
</script>
<style scoped>
nav a {
  @apply font-muoto-medium leading-none w-full py-16 border-b border-b-black flex items-center hover:text-graphite transition-all;
}
</style>