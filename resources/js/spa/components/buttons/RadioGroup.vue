<template>
  <fieldset class="flex flex-col gap-y-8">
    <label 
      :class="classes"
      v-for="option in options" 
      :key="option.value">
      <span :class="labelClasses">{{ option.label }}</span>
      <div class="relative flex items-center justify-center">
        <input
          type="radio"
          :name="name"
          :value="option.value"
          :checked="modelValue === option.value"
          @change="$emit('update:modelValue', option.value)"
          class="sr-only"
        />
        <IconRadio v-if="modelValue !== option.value" />
        <IconRadio variant="checked" v-else />
      </div>
    </label>
  </fieldset>
</template>

<script setup>
import IconRadio from '@/components/icons/Radio.vue';

defineProps({
  modelValue: {
    type: [String, Number],
    required: true
  },
  name: {
    type: String,
    required: true
  },
  options: {
    type: Array,
    required: true,
    validator: (value) => {
      return value.every(option => 
        typeof option === 'object' && 
        'value' in option && 
        'label' in option
      );
    }
  },
  classes: {
    type: String,
    default: 'min-h-default border-y border-graphite pr-8 flex items-center justify-between cursor-pointer group'
  },
  labelClasses: {
    type: String,
    default: 'font-muoto-regular text-md'
  }
});

defineEmits(['update:modelValue']);
</script>