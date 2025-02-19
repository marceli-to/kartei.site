<x-layout.guest>

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
      class="bg-white flex flex-col gap-y-8 w-full">
      
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

</x-layout.guest>

{{-- 
<form method="POST" action="{{ route('auth.register') }}">
  @csrf

  <!-- Name -->
  <div>
      <x-auth.input-label for="name" :value="__('Name')" />
      <x-auth.text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
      <x-auth.input-error :messages="$errors->get('name')" class="mt-2" />
  </div>

  <!-- Email Address -->
  <div class="mt-4">
      <x-auth.input-label for="email" :value="__('Email')" />
      <x-auth.text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
      <x-auth.input-error :messages="$errors->get('email')" class="mt-2" />
  </div>

  <!-- Password -->
  <div class="mt-4">
      <x-auth.input-label for="password" :value="__('Password')" />

      <x-auth.text-input id="password" class="block mt-1 w-full"
                      type="password"
                      name="password"
                      required autocomplete="new-password" />

      <x-auth.input-error :messages="$errors->get('password')" class="mt-2" />
  </div>

  <!-- Confirm Password -->
  <div class="mt-4">
      <x-auth.input-label for="password_confirmation" :value="__('Confirm Password')" />

      <x-auth.text-input id="password_confirmation" class="block mt-1 w-full"
                      type="password"
                      name="password_confirmation" required autocomplete="new-password" />

      <x-auth.input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
  </div>

  <div class="flex items-center justify-end mt-4">
      <a class="underline  text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('auth.login') }}">
          {{ __('Already registered?') }}
      </a>

      <x-auth.primary-button class="ms-4">
          {{ __('Register') }}
      </x-auth.primary-button>
  </div>
</form>
--}}
