<template>
  <h2 class="font-muoto-medium">Lizenzen</h2>
  <div class="leading-normal">
    <h3 class="mt-16 ml-16 font-muoto-medium">Small</h3>
    <ul class="mb-16">
      <li>1 Benutzer/in</li>
      <li>10 Karteien</li>
      <li>Fr. 18.00 / Monat</li>
    </ul>
    <h3 class="mt-16 ml-16 font-muoto-medium">Medium</h3>
    <ul class="mb-16">
      <li>1 – 5 Benutzer / innen</li>
      <li>20 Karteien</li>
      <li>Basispreis: Fr. 25.00 / Monat</li>
      <li>Fr. 15.00 / Benutzer / in + Monat</li>
    </ul>
    <h3 class="mt-16 ml-16 font-muoto-medium">Professional</h3>
    <ul>
      <li>5 – 20 Benutzer / innen</li>
      <li>40 Karteien</li>
      <li>Basispreis: Fr. 50.00 / Monat</li>
      <li>Fr. 12.00 / Benutzer / in + Monat</li>
    </ul>
    <p class="my-24 ml-16">
      Für individuelle Unternehmenslösungen kontaktieren Sie uns:
      <a
        href="mailto:loesungen@kartei.site"
        title="Kontaktieren Sie uns"
        class="underline underline-offset-2 hover:no-underline">
        loesungen@kartei.site
      </a>
    </p>
    <div>
      <a
        href=""
        class="flex items-center gap-x-8 font-muoto-medium">
        <Icon
          name="chevron-right"
          variant="tiny" />
        Vertragsbedingungen
      </a>
    </div>
  </div>
</template>
<script setup>
import Icon from '@/components/icons/Icon.vue';
</script>
<style scoped>
@reference "@/../../css/app.css";
ul {
  @apply list-none;
}

li {
  @apply relative pl-16;
}

li::before {
  @apply absolute left-0 content-['–'];
}
</style>
