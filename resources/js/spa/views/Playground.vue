<template>
  <h1>Playground</h1>
  <p>Count: {{ count }}</p>
  <button @click="increment">Increment</button>
  <FileUpload v-bind="config" />
  <div class="flex items-center gap-x-10 mt-30">
    <IconChevronLeft variant="small" />
    <IconChevronLeft variant="large" />
    <IconChevronRight variant="tiny" />
    <IconChevronRight variant="small" />
    <IconChevronRight variant="large" />
    <IconChevronUp />
    <IconChevronDown />
    <IconCross variant="large" />
    <IconCross variant="small" />
    <IconCross variant="smallBold" />
    <IconCross variant="tiny" />
    <IconHeart variant="fill" />
    <IconHeart variant="outline" />
    <IconPlus variant="bold" />
    <IconPlus />
    <IconSettings />
    <IconSettings variant="tiny" />
    <IconColumns />
    <IconGrid />
    <IconList />
    <IconImage />
    <IconMagnifier />
    <IconInfo />
</div>
</template>

<script setup>
import { useMainStore } from '../store';
import { storeToRefs } from 'pinia';
import ImageUpload from '../components/ImageUpload.vue';
import FileUpload from '../components/FileUpload.vue';
import IconChevronLeft from '../components/icons/ChevronLeft.vue';
import IconChevronRight from '../components/icons/ChevronRight.vue';
import IconChevronUp from '../components/icons/ChevronUp.vue';
import IconChevronDown from '../components/icons/ChevronDown.vue';
import IconCross from '../components/icons/Cross.vue';
import IconHeart from '../components/icons/Heart.vue';
import IconPlus from '../components/icons/Plus.vue';
import IconSettings from '../components/icons/Settings.vue';
import IconColumns from '../components/icons/Columns.vue';
import IconGrid from '../components/icons/Grid.vue';
import IconList from '../components/icons/List.vue';
import IconImage from '../components/icons/Image.vue';
import IconMagnifier from '../components/icons/Magnifier.vue';
import IconInfo from '../components/icons/Info.vue';

const store = useMainStore();
const { count } = storeToRefs(store);

const config = {
  maxSize: 10 * 1024 * 1024, // 10MB
  allowedTypes: ['image/*'],
  uploadUrl: '/api/upload',
  multiple: true
}
const increment = store.increment;
</script>