<template>
  <!-- Layout of the "Löschen" slides (archive, account): text, then a red button -->
  <Slide class="-top-4 pb-36">
    <div class="w-full h-full flex flex-col justify-between">
      <div>
        <slot />
      </div>
      <div v-if="isActive">
        <ButtonGroup>
          <ButtonPrimary 
            type="button"
            :label="label" 
            variant="danger"
            :disabled="disabled"
            @click="$emit('delete')" />
        </ButtonGroup>
      </div>
    </div>
  </Slide>
</template>
<script setup>
import ButtonGroup from '@/components/buttons/Group.vue';
import ButtonPrimary from '@/components/buttons/Primary.vue';
import Slide from '@/components/slider/Slide.vue';

defineProps({
  isActive: {
    type: Boolean,
    default: false
  },
  label: {
    type: String,
    required: true
  },
  disabled: {
    type: Boolean,
    default: false
  }
});

defineEmits(['delete']);
</script>
