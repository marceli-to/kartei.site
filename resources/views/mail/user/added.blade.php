<x-mail::message>
  <div>
    Guten Tag,<br><br>
    Sie wurden zur Kartei <strong>{{ $data['archive_name'] ?? '' }}</strong> hinzugefügt. Sie können sich wie gewohnt mit Ihrem bestehenden Konto anmelden.
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
