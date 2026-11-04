<template>
  <div class="text-sm flex flex-col gap-y-16">
    <template v-if="archives.length">
      <div>
        <h3 class="font-muoto-medium mb-8">Folgende Karteien werden mit allen Karten und Bildern gelöscht:</h3>
        <ul>
          <li v-for="archive in archives" :key="archive.uuid">
            {{ archive.name }} ({{ archive.records_count }} {{ archive.records_count === 1 ? 'Karte' : 'Karten' }})
          </li>
        </ul>
      </div>
    </template>
    <p>Diese Aktion kann nicht rückgängig gemacht werden.</p>
  </div>
</template>
<script setup>
defineProps({
  // [{ uuid, name, records_count }] from /api/user/owned-archives
  archives: {
    type: Array,
    default: () => []
  }
});
</script>
