<header>
  <x-layout.inner class="min-h-80 flex items-center border-b border-b-black">
    @if (Route::is('auth.*'))
      <div class="w-full flex justify-between">
        <a 
          href="{{ route('page.home') }}"
          title="Startseite">
          <x-icons.logo />
        </a>
        <a 
          href="{{ route('page.home') }}"
          title="Startseite"
          class="block">
          <x-icons.cross />
        </a>
      </div>
    @else
      <div class="w-full flex justify-between">
        <a 
          href="{{ route('page.home') }}"
          title="Startseite">
          <x-icons.logo />
        </a>
      </div>
    @endif
  </x-layout.inner>
</header>
