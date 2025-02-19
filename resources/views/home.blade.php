<x-layout.guest>
@section('content')
  <x-layout.inner>
    <a href="{{ route('auth.login') }}">Login</a>
  </x-layout.inner>
@endsection
</x-layout.guest>
