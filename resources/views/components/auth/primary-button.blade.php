<button {{ $attributes->merge(['type' => 'submit', 'class' => 'bg-black text-white transition-all rounded-sm  p-10 transition-all']) }}>
  {{ $slot }}
</button>
