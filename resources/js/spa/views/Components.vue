<template>
<div class="flex flex-col gap-y-32">
  <div class="mt-30">
    <h2>Icons</h2>
    <div class="flex flex-wrap items-center gap-12">
      <IconLogo />
      <IconChevronRight variant="tiny" />
      <IconChevronLeft variant="small" />
      <IconChevronRight variant="small" />
      <IconChevronLeft variant="large" />
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
  </div>
  <div>
    <h2 class="mb-12">Image upload</h2>
    <FileUpload v-bind="config" />
  </div>
  <div>
    <h2 class="mb-12">Fonts</h2>
    <p class="font-otto-bold">ABCOtto bold</p>
  </div>
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