<x-layout.head />
<x-layout.header />
<x-layout.main class="{{ $class ?? '' }}">
  @yield('content')
</x-layout.main>
<x-layout.footer />