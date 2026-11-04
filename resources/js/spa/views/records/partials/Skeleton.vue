<template>
  <div class="flex flex-col gap-y-32 w-3/12 min-w-3/12 text-graphite hover:text-black group transition-colors">
    <ImageCard class="flex justify-center items-center">
      <Icon name="plus" variant="rounded" />
    </ImageCard>
    <div class="border-b border-graphite group-hover:border-black">
      <div class="min-h-default flex items-center border-t border-graphite group-hover:border-black justify-between font-muoto-medium">
        <span>Nr./ID</span>
        <Icon name="heart" variant="outline" class="text-graphite" />
      </div>
      <div class="min-h-default flex items-center border-t border-graphite group-hover:border-black font-muoto-italic">
        <span>Grafiker</span>
      </div>
      <div class="min-h-default flex items-center border-t border-graphite group-hover:border-black font-muoto-italic">
        <span>Jahr</span>
      </div>
    </div>
  </div>
</template>
<script setup>
import ImageCard from '@/components/media/Card.vue';
import Icon from '@/components/icons/Icon.vue';
</script>
