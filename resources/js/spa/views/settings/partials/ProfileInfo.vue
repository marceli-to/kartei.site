<template>
  <h2 class="font-muoto-medium">Passwortstärke</h2>
  <div class="leading-normal">
    <ul>
      <li>Gross- und Kleinbuchstaben</li>
      <li>mind. 1 Zahl</li>
      <li>mind. 1 Symbol</li>
    </ul>
  </div>
</template>
<style scoped>
@reference "@/../../css/app.css";
ul {
  @apply list-none;
}

li {
  @apply relative pl-16;
}

li::before {
  @apply absolute left-0 content-['–'];
}
</style>
