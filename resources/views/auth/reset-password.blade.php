<x-layout.guest class="mt-108 flex justify-center">
@section('content')

  @if ($errors->any())
    <x-auth.toast status="Es ist ein Fehler aufgetreten." type="error" />
  @endif

  <x-auth.wrapper>

    <form 
      method="POST" 
      action="{{ route('auth.password.store') }}"
      class="flex flex-col gap-y-8 w-full">
      @csrf

      <!-- Password Reset Token -->
      <input type="hidden" name="token" value="{{ $request->route('token') }}">

      <x-auth.input-label>
        {{ __('Passwort zurücksetzen') }}
      </x-auth.input-label>

      <x-auth.text-input 
        type="email" 
        name="email" 
        :value="old('email', $request->email)"
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
        {{ __('Passwort zurücksetzen') }}
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
