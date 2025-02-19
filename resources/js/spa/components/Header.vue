<template>
  <header class="relative z-90">
    <div class="min-h-80 flex items-center border-b border-b-black">
      <div class="w-full flex items-center justify-between">
        <router-link :to="{ name: 'home' }">
          <IconLogo />
        </router-link>
        <div class="flex justify-end items-center gap-x-48">
          <a href="">
            <IconInfo />
          </a>
          <a href="">
            <IconProfile />
          </a>
          <button 
            class="w-28 h-24 flex items-center justify-center"
            @click="menuRef.toggle()">
            <IconCross v-if="menuRef?.isOpen" />
            <IconBurger v-else />
          </button>
        </div>
      </div>
    </div>
  </header>
  <Menu ref="menuRef" />
</template>

<script setup>
import { ref } from 'vue';
import IconLogo from '@/components/icons/Logo.vue';
import IconInfo from '@/components/icons/Info.vue';
import IconProfile from '@/components/icons/Profile.vue';
import IconBurger from '@/components/icons/Burger.vue';
import IconCross from '@/components/icons/Cross.vue';
import Menu from '@/components/Menu.vue';

const menuRef = ref(null);
</script>