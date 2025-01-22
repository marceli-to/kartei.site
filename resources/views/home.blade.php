<x-layout.app>
@section('content')
  <x-layout.inner>
    <h1 class="text-3xl">kartei.site</h1>
    <a href="{{ route('auth.login') }}">Login</a>
  </x-layout.inner>
@endsection
</x-layout.app>
