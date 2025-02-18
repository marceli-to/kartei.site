@props(['class' => ''])
<main role="main" class="flex-1 flex flex-col {{ Route::is('auth.*') ? 'justify-center items-center' : '' }} {{ $class ?? '' }}">
  {{ $slot }}
</main>