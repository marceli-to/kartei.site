<x-layout.guest class="mt-108 flex justify-center">
@section('content')
  @if ($errors->any())
    <x-auth.toast status="Es ist ein Fehler aufgetreten." type="error" />
  @endif

  @if (session('status'))
    <x-auth.toast :status="session('status')" type="success" />
  @endif

  <x-auth.wrapper>
  
    <form 
      method="POST" 
      action="{{ route('auth.login') }}" 
      class="flex flex-col gap-y-8 w-full">
      @csrf

      <x-auth.input-label>
        {{ __('Anmelden') }}
      </x-auth.input-label>

      <x-auth.text-input 
        type="email" 
        name="email" 
        :value="old('email')"
        data-error="{{ $errors->has('email') ? 'true' : null }}"
        placeholder="{{ __('E-Mail') }}"
        required />

      <x-auth.text-input 
        type="password"
        name="password"
        placeholder="{{ __('Passwort') }}"
        data-error="{{ $errors->has('password') ? 'true' : null }}"
        required />

      <x-auth.primary-button>
        {{ __('Login') }}
      </x-auth.primary-button>

      <div class="flex justify-between mt-8">

        @if (Route::has('auth.password.request'))
          <x-auth.helper-link :route="'auth.password.request'">
            {{ __('Passwort vergessen?') }}
          </x-auth.helper-link>
        @endif

        @if (Route::has('auth.register'))
          <x-auth.helper-link :route="'auth.register'">
            {{ __('Noch nicht registriert?') }}
          </x-auth.helper-link>
        @endif

      </div>

    </form>
  
  </x-auth.wrapper>
@endsection
</x-layout.guest>
