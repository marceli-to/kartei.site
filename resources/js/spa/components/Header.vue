<template>
  <header class="relative z-90">
    <div class="min-h-80 flex border-b border-b-black">
      <div class="w-full flex justify-between leading-none">
        <router-link 
          :to="{ name: 'home' }" 
          class="mt-25" 
          @click="sidebar.hide">
          <IconLogo />
        </router-link>
        <div class="flex justify-end gap-x-48">
          <a href="" class="mt-20 group">
            <IconInfo classes="group-hover:hidden" />
            <IconInfo variant="active" classes="hidden group-hover:block" />
          </a>
          <a href="" class="mt-20 group">
            <IconProfile classes="group-hover:hidden" />
            <IconProfile variant="active" classes="hidden group-hover:block" />
          </a>
          <button 
            class="w-28 h-24 mt-33 flex items-center justify-center"
            @click="sidebar.toggle">
            <IconCross v-if="sidebar.isOpen.value" />
            <IconBurger v-else />
          </button>
        </div>
      </div>
    </div>
  </header>
  <SidebarMenu :is-open="sidebar.isOpen.value" @toggle="sidebar.toggle" />
</template>
<script setup>
import IconLogo from '@/components/icons/Logo.vue';
import IconInfo from '@/components/icons/Info.vue';
import IconProfile from '@/components/icons/Profile.vue';
import IconBurger from '@/components/icons/Burger.vue';
import IconCross from '@/components/icons/Cross.vue';
import SidebarMenu from '@/components/menu/Sidebar.vue';
import { useToggleSidebar } from '@/composables/useToggleSidebar'
const sidebar = useToggleSidebar();
</script>