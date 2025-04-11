<template>
  <div class="flex flex-col gap-y-32 w-3/12 min-w-3/12 opacity-40 hover:opacity-100 transition-opacity">
    <ImageCard class="flex justify-center items-center">
      <IconPlus variant="rounded" class="text-graphite" />
    </ImageCard>
    <div class="border-b border-graphite">
      <div class="min-h-default flex items-center border-t border-graphite justify-between font-muoto-medium">
        <span>Nr./ID</span>
        <IconHeart variant="outline" class="text-graphite" />
      </div>
      <div class="min-h-default flex items-center border-t border-graphite font-muoto-italic">
        <span>Grafiker</span>
      </div>
      <div class="min-h-default flex items-center border-t border-graphite font-muoto-italic">
        <span>Jahr</span>
      </div>
    </div>
  </div>
</template>
<script setup>
import ImageCard from '@/components/media/Card.vue';
import IconPlus from '@/components/icons/Plus.vue';
import IconHeart from '@/components/icons/Heart.vue';
</script>
