<template>
<div class="flex flex-col gap-y-32">
  <div class="mt-30">
    <h2 class="font-muoto-medium">Icons</h2>
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
      <IconPlus variant="rounded" class="text-graphite" />
      <IconSettings />
      <IconSettings variant="tiny" />
      <IconImage />
      <IconImage variant="missing" />
      <IconMagnifier />
      <IconEye variant="enabled" />
      <IconEye variant="disabled" />
      <IconRadio />
      <IconRadio variant="checked" />
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
    <h2 class="mb-12 font-muoto-medium">Image upload</h2>
    <FileUpload v-bind="config" />
  </div>
  <div>
    <h2 class="mb-12 font-muoto-medium">Fonts</h2>
    <p class="font-otto-bold">ABCOtto bold [font-otto-bold]</p>
    <p class="font-otto-regular">ABCOtto regular [font-otto-regular]</p>
    <p class="font-muoto-regular">Muoto regular [font-muoto-regular]</p>
    <p class="font-muoto-medium">Muoto medium [font-muoto-medium]</p>
    <p class="font-muoto-italic">Muoto italic [font-muoto-italic]</p>
  </div>
  <div>
    <h2 class="mb-12 font-muoto-medium">Color swatches</h2>
    <div class="flex flex-col gap-12">
      <div class="flex items-center gap-x-8">
        <span class="rounded-full block size-12 leading-none bg-graphite"></span>
        graphite [#78786E]
      </div>
      <div class="flex items-center gap-x-8">
        <span class="rounded-full block size-12 leading-none bg-ash"></span>
        ash [#96968C]
      </div>
      <div class="flex items-center gap-x-8">
        <span class="rounded-full block size-12 leading-none bg-pebble"></span>
        pebble [#D2D2C8]
      </div>
      <div class="flex items-center gap-x-8">
        <span class="rounded-full block size-12 leading-none bg-snow"></span>
        snow [#F5F5F3]
      </div>
      <div class="flex items-center gap-x-8">
        <span class="rounded-full block size-12 leading-none bg-ice"></span>
        ice [#96E6FF]
      </div>
      <div class="flex items-center gap-x-8">
        <span class="rounded-full block size-12 leading-none bg-candy"></span>
        candy [#FF8CD2]
      </div>
      <div class="flex items-center gap-x-8">
        <span class="rounded-full block size-12 leading-none bg-lemon"></span>
        lemon [#EBF000]
      </div>
      <div class="flex items-center gap-x-8">
        <span class="rounded-full block size-12 leading-none bg-lime"></span>
        lime [#B4E600]
      </div>
      <div class="flex items-center gap-x-8">
        <span class="rounded-full block size-12 leading-none bg-flame"></span>
        flame [#FF3200]
      </div>
      <div class="flex items-center gap-x-8">
        <span class="rounded-full block size-12 leading-none bg-onyx"></span>
        onyx [#1E1E1E]
      </div>
      <div class="flex items-center gap-x-8">
        <span class="rounded-full block size-12 leading-none bg-charcoal"></span>
        charcoal [#464644]
      </div>
      <div class="flex items-center gap-x-8">
        <span class="rounded-full block size-12 leading-none bg-steel"></span>
        steel [#8C8C8C]
      </div>
    </div>
  </div>
</div>
</template>

<script setup>
import ImageUpload from '../components/ImageUpload.vue';
import FileUpload from '../components/FileUpload.vue';
import IconLogo from '../components/icons/Logo.vue';
import IconChevronLeft from '../components/icons/ChevronLeft.vue';
import IconChevronRight from '../components/icons/ChevronRight.vue';
import IconChevronUp from '../components/icons/ChevronUp.vue';
import IconChevronDown from '../components/icons/ChevronDown.vue';
import IconCross from '../components/icons/Cross.vue';
import IconHeart from '../components/icons/Heart.vue';
import IconRadio from '../components/icons/Radio.vue';
import IconEye from '../components/icons/Eye.vue';
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

const config = {
  maxSize: 10 * 1024 * 1024, // 10MB
  allowedTypes: ['image/*'],
  uploadUrl: '/api/upload',
  multiple: true
}
</script>