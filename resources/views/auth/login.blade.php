<x-layout.guest>

  <x-auth.wrapper>
  
    <x-auth.session-status class="mb-10" :status="session('status')" />
  
    <form 
      method="POST" 
      action="{{ route('auth.login') }}" 
      class="bg-white flex flex-col gap-y-16 w-full">
      @csrf

      <div class="w-full">
        <x-auth.input-label for="email" :value="__('E-Mail')" />
        <x-auth.text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
        <x-auth.input-error :messages="$errors->get('email')" class="block mt-8" />
      </div>

      <div>
        <x-auth.input-label for="password" :value="__('Passwort')" />
        <x-auth.text-input 
          id="password"
          type="password"
          name="password"
          required autocomplete="current-password" />
        <x-auth.input-error :messages="$errors->get('password')" class="block mt-8" />
      </div>

      <div class="block mt-10">
        <label for="remember_me" class="inline-flex items-center">
          <input id="remember_me" type="checkbox" class="border-black border-2 text-black shadow-sm focus:!ring-0 !ring-0" name="remember">
          <span class="ms-8  text-gray-600">{{ __('Remember me') }}</span>
        </label>
      </div>

      <div class="flex items-center justify-between">
        
        @if (Route::has('auth.password.request'))
          <a class="underline  focus:outline-none" href="{{ route('auth.password.request') }}">
            {{ __('Forgot your password?') }}
          </a>
        @endif

        <x-auth.primary-button class="ms-3">
          {{ __('Login') }}
        </x-auth.primary-button>
      </div>

    </form>
  
  </x-auth.wrapper>
  
</x-layout.guest>
