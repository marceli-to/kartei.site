@props(['value'])
<label class="block leading-none font-muoto-medium text-sm">
  {{ $value ?? $slot }}
</label>