<x-layout.guest>
  
  <x-auth.wrapper>

    <div class="mb-16">
      {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
    </div>

    <x-auth.session-status class="mb-4" :status="session('status')" />

    <form 
      method="POST" 
      action="{{ route('password.email') }}"
      class="bg-white flex flex-col gap-y-16 w-full">
      @csrf

      <div>
        <x-auth.input-label for="email" :value="__('Email')" />
        <x-auth.text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
        <x-auth.input-error :messages="$errors->get('email')" class="mt-2" />
      </div>

      <div class="flex items-center justify-end mt-4">
        <x-auth.primary-button>
          {{ __('Email Password Reset Link') }}
        </x-auth.primary-button>
      </div>

    </form>
  
  </x-auth.wrapper>

</x-layout.guest>
