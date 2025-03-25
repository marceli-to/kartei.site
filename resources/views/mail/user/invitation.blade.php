<x-mail::message>
  <div>
    Guten Tag,<br><br>
    Sie wurden eingeladen, Kartei.site zu nutzen. Ihr Konto wurde bereits erstellt und Sie können sich mit folgenden Informationen anmelden:
    <br><br>
    <strong>E-Mail:</strong> {{ isset($data['email']) ? $data['email'] : 'Ihre E-Mail-Adresse' }}<br>
    <strong>Passwort:</strong> {{ config('user.welcome_password') }}
    <br><br>
    Bitte ändern Sie Ihr Passwort nach der ersten Anmeldung.
    <br><br>
    <x-mail::button :url="url('/')">
      Zur Anmeldung
    </x-mail::button>
    <br>
    Bei Fragen stehen wir Ihnen gerne zur Verfügung.
    <br><br>
    Freundliche Grüsse<br>kartei.site
  </div>
</x-mail::message>
