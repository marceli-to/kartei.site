@props(['class' => ''])
<main role="main" class="flex-1 flex flex-col justify-center items-center py-30 {{ $class ?? '' }}">
  {{ $slot }}
</main>