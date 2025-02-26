<template>
  <h2 class="font-muoto-medium">Lizenzen</h2>
  <div class="leading-[1.5]">
    <h3 class="font-muoto-medium ml-16 mt-16">Small</h3>
    <ul class="mb-16">
      <li>1 Benutzer/in</li>
      <li>10 Karteien</li>
      <li>Fr. 18.00 / Monat</li>
    </ul>
    <h3 class="font-muoto-medium ml-16 mt-16">Medium</h3>
    <ul class="mb-16">
      <li>1 – 5 Benutzer / innen</li>
      <li>20 Karteien</li>
      <li>Basispreis: Fr. 25.00 / Monat</li>
      <li>Fr. 15.00 / Benutzer / in + Monat</li>
    </ul>
    <h3 class="font-muoto-medium ml-16 mt-16">Professional</h3>
    <ul>
      <li>5 – 20 Benutzer / innen</li>
      <li>40 Karteien</li>
      <li>Basispreis: Fr. 50.00 / Monat</li>
      <li>Fr. 12.00 / Benutzer / in + Monat</li>
    </ul>
    <p class="ml-16 my-24">Für individuelle Unternehmens- lösungen kontaktieren Sie uns: lösungen@kartei.site</p>
    <div>
      <a href="" class="flex gap-x-8 items-center font-muoto-medium">
        <IconChevronRight variant="tiny" />
        Vertragsbedingungen
      </a>
    </div>
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