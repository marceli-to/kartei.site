@props(['value'])

<label {{ $attributes->merge(['class' => 'block mb-10']) }}>
  {{ $value ?? $slot }}
</label>
