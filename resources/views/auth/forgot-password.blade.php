<x-layout.guest class="mt-108 flex justify-center">
@section('content')
  
  @if (session('status'))
    <x-auth.toast :status="session('status')" type="success" />
  @endif

  @if ($errors->any())
    <x-auth.toast :status="$errors->first()" type="error" />
  @endif

  <x-auth.wrapper>

    <div class="font-muoto-medium text-sm mb-16">
      {{ __('Sie haben Ihr Passwort vergessen? Das ist kein Problem. Teilen Sie uns einfach Ihre E-Mail-Adresse mit und wir senden Ihnen einen Link zum Zurücksetzen des Passworts zu, mit dem Sie ein neues wählen können.') }}
    </div>

    <form 
      method="POST" 
      action="{{ route('auth.password.email') }}"
      class="flex flex-col gap-y-8 w-full">
      @csrf

      <x-auth.text-input 
        type="email" 
        name="email" 
        :value="old('email')"
        data-error="{{ $errors->has('email') ? 'true' : null }}"
        placeholder="E-Mail"
        required
        autocomplete="email" />

        <x-auth.primary-button>
          {{ __('E-Mail-Link anfordern') }}
        </x-auth.primary-button>

        @if (Route::has('auth.login'))
          <div class="flex justify-between mt-8">
            <x-auth.helper-link :route="'auth.login'">
              {{ __('Zurück zum Login') }}
            </x-auth.helper-link>
          </div>
        @endif

    </form>
  
  </x-auth.wrapper>
@endsection
</x-layout.guest>
