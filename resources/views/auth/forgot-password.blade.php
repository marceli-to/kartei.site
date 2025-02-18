<x-layout.guest>
  <x-auth.toast :status="$errors->any() ? 'Es ist ein Fehler aufgetreten – bitte Überprüfen Sie ihre Angaben.' : null" />

  <x-auth.wrapper>

    <div class="font-muoto-medium text-sm mb-16">
      {{ __('Sie haben Ihr Passwort vergessen? Das ist kein Problem. Teilen Sie uns einfach Ihre E-Mail-Adresse mit und wir senden Ihnen einen Link zum Zurücksetzen des Passworts zu, mit dem Sie ein neues wählen können.') }}
    </div>

    
    <form 
      method="POST" 
      action="{{ route('auth.password.email') }}"
      class="bg-white flex flex-col gap-y-8 w-full">
      @csrf

      <x-auth.text-input 
        type="email" 
        name="email" 
        :value="old('email')"
        data-error="{{ $errors->has('email') ? 'true' : null }}"
        placeholder="E-Mail"
        autocomplete="email" />

        <x-auth.primary-button>
          {{ __('E-Mail-Link anfordern') }}
        </x-auth.primary-button>

    </form>
  
  </x-auth.wrapper>

</x-layout.guest>
