@props(['text' => null, 'route'])
<a 
  class="text-sm hover:underline underline-offset-2" 
  href="{{ route($route) }}">
  {{ $slot }}
</a>