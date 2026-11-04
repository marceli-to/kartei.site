<template>
  <div class="group flex w-3/12 min-w-3/12 flex-col gap-y-32 text-graphite transition-colors hover:text-black">
    <ImageCard class="flex items-center justify-center">
      <Icon
        name="plus"
        variant="rounded" />
    </ImageCard>
    <div class="border-b border-graphite group-hover:border-black">
      <div
        class="flex min-h-default items-center justify-between border-t border-graphite font-muoto-medium group-hover:border-black">
        <span>Nr./ID</span>
        <Icon
          name="heart"
          variant="outline"
          class="text-graphite" />
      </div>
      <div class="flex min-h-default items-center border-t border-graphite font-muoto-italic group-hover:border-black">
        <span>Grafiker</span>
      </div>
      <div class="flex min-h-default items-center border-t border-graphite font-muoto-italic group-hover:border-black">
        <span>Jahr</span>
      </div>
    </div>
  </div>
</template>
<script setup>
import ImageCard from '@/components/media/Card.vue';
import Icon from '@/components/icons/Icon.vue';
</script>
