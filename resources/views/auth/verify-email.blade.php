<x-layout.guest class="mt-108 flex justify-center">
@section('content')

  @if (session('status') == 'verification-link-sent')
    <x-auth.toast :status="__('Ein neuer Verifizierungslink wurde an die E-Mail-Adresse geschickt, die Sie bei der Registrierung angegeben haben.')" type="success" />
  @endif

  <x-auth.wrapper>

    <p class="text-sm mb-16">
      {{ __('Vielen Dank für die Anmeldung! Bevor Sie beginnen, könnten Sie Ihre E-Mail-Adresse verifizieren, indem Sie auf den Link klicken, den wir Ihnen soeben per E-Mail geschickt haben? Wenn Sie die E-Mail nicht erhalten haben, schicken wir Ihnen gerne eine neue zu.') }}
    </p>

    <form method="POST" action="{{ route('auth.verification.send') }}">
      @csrf
      <div>
        <x-auth.primary-button>
          {{ __('E-Mail erneut senden') }}
        </x-auth.primary-button>
      </div>
    </form>

  </x-auth.wrapper>

@endsection
</x-layout.guest>
