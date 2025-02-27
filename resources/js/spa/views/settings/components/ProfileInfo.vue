<template>
  <h2 class="font-muoto-medium">Passwortstärke</h2>
  <div class="leading-[1.5]">
    <ul>
      <li>Gross- und Kleinbuchstaben</li>
      <li>mind. 1 Zahl</li>
      <li>mind. 1 Symbol</li>
    </ul>
  </div>
</template>
<script setup>
import IconChevronRight from '@/components/icons/ChevronRight.vue';
</script>
<style scoped>
  ul {
    @apply list-none;
  }

  li {
    @apply relative pl-16
  }

  li::before {
    @apply content-['–'] absolute left-0;
  }
</style>