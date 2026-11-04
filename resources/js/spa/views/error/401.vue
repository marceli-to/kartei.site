<template>
  <Message>
    <template #title>
      401
    </template>
    <template #message>
      <p class="text-center">
        Sitzung abgelaufen<br>Sie werden in {{ countdown }} Sekunde<span v-if="countdown !== 1">n</span> weitergeleitet.
      </p>
    </template>
  </Message>
</template>
 <script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import Message from '@/views/error/ErrorMessage.vue'

// Reached when an API request gets a 401 (session expired); the login
// page sends the user back to /archiv afterwards
const countdown = ref(5)
let interval = null

onUnmounted(() => clearInterval(interval))

onMounted(() => {
  interval = setInterval(() => {
    countdown.value--
    if (countdown.value === 0) {
      clearInterval(interval)
      document.location.href = '/login'
    }
  }, 1000)
})
</script>
