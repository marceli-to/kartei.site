<template>
  <div class="pr-12 hyphens-auto">
    <p>
      Einer Kartei können beliebig viele Kategorien zugewiesen werden, die Kategorien wiederum können in Register
      unterteilt werden.
    </p>
    <p>
      Standardmässig werden Kategorien und Register numerisch geordnet, in den Einstellungen
      <span class="whitespace-nowrap"
        >(<Icon
          name="settings"
          variant="tiny"
          class="-mt-1 inline-block" />)</span
      >
      stehen weitere Ordnungsstrukturen zur Auswahl.
    </p>
    <p>
      Standardmässig besteht die ID aus den ersten beiden Buchstaben des Registertitels. In den Einstellungen
      <span class="whitespace-nowrap"
        >(<Icon
          name="settings"
          variant="tiny"
          class="-mt-1 inline-block" />)</span
      >
      finden Sie alternativ die Grundeinstellung für ein leeres Textfeld.
    </p>
  </div>
</template>
<script setup>
import Icon from '@/components/icons/Icon.vue';
</script>
