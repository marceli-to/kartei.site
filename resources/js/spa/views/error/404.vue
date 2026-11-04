<template>
  <Message>
    <template #title>
      404
    </template>
    <template #message>
      <p class="text-center">
        Seite nicht gefunden<br>
        Keine Sorge, hier geht es zurück:
        <span class="flex justify-center mt-16">
          <router-link :to="{ name: 'archives' }" aria-label="Zurück zu Meine Karteien">
            <IconChevronLeft variant="large-rounded" />
          </router-link>
        </span>
      </p>
    </template>
  </Message>
</template>
<script setup>
import Message from '@/views/error/ErrorMessage.vue';
import IconChevronLeft from '@/components/icons/ChevronLeft.vue';
</script>
  