<template>
  <Message>
    <template #title>
      419
    </template>
    <template #message>
      <p class="text-center">
        Sitzung abgelaufen<br>Sie werden in {{ countdown }} Sekunde<span v-if="countdown !== 1">n</span> weitergeleitet.
      </p>
    </template>
  </Message>
</template>
 <script setup>
import { ref, onMounted } from 'vue'
import Message from '@/views/error/ErrorMessage.vue'
const countdown = ref(5)

onMounted(() => {
  const interval = setInterval(() => {
    countdown.value--
    if (countdown.value === 0) {
      clearInterval(interval)
      document.location.href = '/login'
    }
  }, 1000)
})
</script>
