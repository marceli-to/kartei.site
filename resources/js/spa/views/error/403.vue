<template>
  <Message>
    <template #title>
      403
    </template>
    <template #message>
      <p class="text-center">
        Autorisation fehlgeschlagen<br>
        Keine Sorge, hier geht es zurück:
        <span class="flex justify-center mt-16">
          <router-link :to="{ name: 'archives' }">
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
  