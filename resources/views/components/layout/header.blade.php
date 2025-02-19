<header>
  <x-layout.inner class="min-h-80 flex items-center border-b border-b-black">
    <div class="w-full flex items-center justify-between">
      @if (Route::is('auth.*'))
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
      @else
        <a 
          href="{{ route('page.home') }}"
          title="Startseite">
          <x-icons.logo />
        </a>
      @endif
    </div>
  </x-layout.inner>
</header>
