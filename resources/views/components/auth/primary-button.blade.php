<button {{ $attributes->merge(['type' => 'submit', 'class' => 'border border-graphite min-h-default font-muoto-regular text-md text-white w-full flex items-center justify-start gap-x-12 px-8 bg-pebble hover:bg-ice hover:text-black !ring-0 !outline-none transition-all']) }}>
  <x-icons.chevron-right />
  {{ $slot }}
</button>
