@props(['class' => ''])
<main role="main" class="{{ $class ?? '' }}">
  {{ $slot }}
</main>