<template>
  <h1>Playground</h1>
  <p>Count: {{ count }}</p>
  <button @click="increment">Increment</button>
  <FileUpload v-bind="config" />
  <div class="flex items-center gap-x-10 mt-30">
    <IconLogo />
    <IconChevronLeft variant="small" />
    <IconChevronLeft variant="large" />
    <IconChevronRight variant="tiny" />
    <IconChevronRight variant="small" />
    <IconChevronRight variant="large" />
    <IconChevronUp />
    <IconChevronDown />
    <IconCross variant="large" />
    <IconCross variant="small" />
    <IconCross variant="small-bold" />
    <IconCross variant="tiny" />
    <IconHeart variant="fill" />
    <IconHeart variant="outline" />
    <IconPlus variant="small" />
    <IconPlus variant="small-bold" />
    <IconSettings />
    <IconSettings variant="tiny" />
    <IconImage />
    <IconImage variant="missing" />
    <IconMagnifier />
    <IconInfo />
    <IconInfo variant="active" />
    <IconProfile />
    <IconProfile variant="active" />
    <IconEdit />
    <IconCross variant="square" />
    <IconList variant="square" />
    <IconPlus variant="square" />
    <IconRefresh variant="square" />
    <IconImage variant="square" />
    <IconUpload variant="square" />
    <IconDownload />
</div>
</template>

<script setup>
import { useMainStore } from '../store';
import { storeToRefs } from 'pinia';
import ImageUpload from '../components/ImageUpload.vue';
import FileUpload from '../components/FileUpload.vue';
import IconLogo from '../components/icons/Logo.vue';
import IconChevronLeft from '../components/icons/ChevronLeft.vue';
import IconChevronRight from '../components/icons/ChevronRight.vue';
import IconChevronUp from '../components/icons/ChevronUp.vue';
import IconChevronDown from '../components/icons/ChevronDown.vue';
import IconCross from '../components/icons/Cross.vue';
import IconHeart from '../components/icons/Heart.vue';
import IconPlus from '../components/icons/Plus.vue';
import IconSettings from '../components/icons/Settings.vue';
import IconList from '../components/icons/List.vue';
import IconRefresh from '../components/icons/Refresh.vue';
import IconImage from '../components/icons/Image.vue';
import IconMagnifier from '../components/icons/Magnifier.vue';
import IconInfo from '../components/icons/Info.vue';
import IconProfile from '../components/icons/Profile.vue';
import IconDownload from '../components/icons/Download.vue';
import IconUpload from '../components/icons/Upload.vue';
import IconEdit from '../components/icons/Edit.vue';

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