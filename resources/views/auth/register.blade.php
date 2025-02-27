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
      action="{{ route('auth.register') }}"
      class="flex flex-col gap-y-8 w-full">
      
      @csrf

      <x-auth.input-label>
        {{ __('Registrieren') }}
      </x-auth.input-label>

      <x-auth.text-input 
        id="name" 
        type="text" 
        name="firstname" 
        :value="old('firstname')"
        data-error="{{ $errors->has('firstname') ? 'true' : null }}"
        placeholder="{{ __('Vorname') }}" 
        required 
        autofocus 
        autocomplete="name" />

      <x-auth.text-input 
        id="name" 
        type="text" 
        name="name" 
        :value="old('name')"
        data-error="{{ $errors->has('name') ? 'true' : null }}"
        placeholder="{{ __('Name') }}" 
        required 
        autofocus 
        autocomplete="name" />

      <x-auth.text-input 
        type="email" 
        name="email" 
        :value="old('email')"
        data-error="{{ $errors->has('email') ? 'true' : null }}"
        placeholder="E-Mail"
        required
        autocomplete="email" />

      <x-auth.text-input 
        type="password"
        name="password"
        placeholder="{{ __('Passwort') }}"
        data-error="{{ $errors->has('password') ? 'true' : null }}"
        required
        autocomplete="new-password" />

      <x-auth.text-input 
        type="password"
        name="password_confirmation"
        placeholder="{{ __('Passwort wiederholen') }}"
        data-error="{{ $errors->has('password_confirmation') ? 'true' : null }}"
        required
        autocomplete="new-password" />

        <x-auth.primary-button>
          {{ __('Registrieren') }}
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
@endSection
</x-layout.guest>

