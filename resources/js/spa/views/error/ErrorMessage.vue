<template>
  <div class="flex justify-center items-center text-center font-otto-bold text-lg text-flame">
    <div>
      <h1 class="text-huge font-otto-regular">
        <slot name="title"></slot>
      </h1>
      <div>
        <slot name="message"></slot>
      </div>
    </div>
  </div>
</template>
<script setup>
import { usePageTitle } from '@/composables/usePageTitle';

const { setTitle } = usePageTitle();
setTitle('');
</script>

  