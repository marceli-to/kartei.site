<template>
  <nav class="bg-gray-100 p-16 flex justify-between">
    <ul class="flex gap-x-16 list-none p-0">
      <li>
        <router-link to="/archiv">Home</router-link>
      </li>
      <li>
        <router-link to="/archiv/karteien">Karteien</router-link>
      </li>
      <li>
        <router-link to="/archiv/benutzer">Users</router-link>
      </li>
      <li>
        <router-link to="/archiv/icons">Icons</router-link>
      </li>
      <li>
        <router-link to="/archiv/fonts">Fonts</router-link>
      </li>
      <li>
        <router-link to="/archiv/colors">Colors</router-link>
      </li>
      <li>
        <router-link to="/archiv/elements">Elements</router-link>
      </li>
    </ul>
    <ul>
      <li>
        <a href="/logout">Logout</a>
      </li>
    </ul>
  </nav>
</template>
<script setup>
</script>