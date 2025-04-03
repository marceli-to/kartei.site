<template>
  <Message>
    <div>419 – Sitzung abgelaufen</div>
    <span class="text-sm">
      Sie werden in {{ countdown }} Sekunde<span v-if="countdown !== 1">n</span> weitergeleitet.
    </span>
  </Message>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import Message from '@/views/error/partials/message.vue'

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
