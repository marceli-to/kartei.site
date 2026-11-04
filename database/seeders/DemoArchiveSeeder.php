<?php
namespace Database\Seeders;

use App\Actions\Archive\Delete as DeleteArchiveAction;
use App\Actions\Image\Resize as ResizeImageAction;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\Media;
use App\Models\Record;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Demo data: 10 archives, each with categories, registers, record fields,
 * tags, records and images (cover + 0–4 per record).
 *
 *   php artisan db:seed --class=DemoArchiveSeeder
 *
 * Re-running replaces the demo archives (matched by name). Images come from
 * picsum.photos, cached in storage/app/seed-images; without network a
 * generated placeholder is used instead.
 */
class DemoArchiveSeeder extends Seeder
{
  private const POOL_SIZE = 40;
  private const POOL_DIR = 'seed-images';

  /** @var array<int, array{original: string, resized: string, width: int, height: int, aspect_ratio: string, mime_type: string, size: int}> */
  private array $pool = [];

  public function run(): void
  {
    mt_srand(4711);

    $this->command?->info('Preparing image pool …');
    $this->preparePool();

    // Account owners (self-registered users) get the demo archives
    $owners = User::whereNull('created_by')->get();
    $favoriteUser = User::where('email', 'm@marceli.to')->first() ?? $owners->first();

    foreach ($this->archives() as $definition) {
      Archive::where('name', $definition['name'])->get()
        ->each(fn (Archive $archive) => (new DeleteArchiveAction())->execute($archive));

      DB::transaction(function () use ($definition, $owners, $favoriteUser) {
        $archive = $this->createArchive($definition);
        $owners->each(fn (User $user) => $archive->users()->attach($user->id, ['added_at' => now()]));

        $count = $this->createRecords($archive, $definition, $favoriteUser);
        $this->command?->info(sprintf('  %s: %d records', $archive->name, $count));
      });
    }
  }

  private function createArchive(array $definition): Archive
  {
    $fields = collect($definition['fields'])->keys()->values()->map(fn ($placeholder, $i) => [
      'uuid' => (string) Str::uuid(),
      'placeholder' => $placeholder,
      'order' => $i,
    ])->all();

    $archive = Archive::create([
      'name' => $definition['name'],
      'acronym' => $definition['acronym'],
      'settings' => ['has_images' => true, 'record_fields' => $fields],
    ]);

    $this->attachImage($archive, $archive);

    return $archive;
  }

  private function createRecords(Archive $archive, array $definition, ?User $favoriteUser): int
  {
    $tags = collect($definition['tags'])->map(fn ($name) => Tag::create(['name' => $name, 'archive_id' => $archive->id]));
    $fields = $archive->settings['record_fields'];
    $values = array_values($definition['fields']);

    // Records are numbered by created_at within their category
    $createdAt = now()->subDays(400);
    $count = 0;

    foreach ($this->createCategories($archive, $definition) as $category) {
      $records = mt_rand(3, 7);
      for ($i = 0; $i < $records; $i++) {
        $createdAt = $createdAt->copy()->addHours(mt_rand(2, 60));

        $record = new Record(['archive_id' => $archive->id, 'category_id' => $category->id]);
        $record->created_at = $record->updated_at = $createdAt;
        $record->save();

        foreach ($fields as $f => $field) {
          // Leave a few fields empty, like real data
          if ($f > 0 && mt_rand(1, 6) === 1) {
            continue;
          }
          $record->fields()->create([
            'uuid' => $field['uuid'],
            'placeholder' => $field['placeholder'],
            'content' => Str::limit($this->value($values[$f]), 250, ''),
          ]);
        }

        if ($tags->isNotEmpty()) {
          $record->tags()->attach($tags->random(min(mt_rand(0, 3), $tags->count()))->pluck('id'));
        }

        $images = [0, 1, 1, 1, 2, 2, 3, 4][mt_rand(0, 7)];
        for ($p = 0; $p < $images; $p++) {
          $this->attachImage($record, $archive, $p);
        }

        if ($favoriteUser && mt_rand(1, 8) === 1) {
          Favorite::create(['record_id' => $record->id, 'user_id' => $favoriteUser->id]);
        }

        $count++;
      }
    }

    return $count;
  }

  /**
   * Same structure as the category editor saves: categories ordered, their
   * registers numbered "<category>.<register>", global_order flat.
   *
   * @return array<int, Category> the categories that hold records (registers, or the category itself if it has none)
   */
  private function createCategories(Archive $archive, array $definition): array
  {
    [$catType, $regType] = $definition['numerals'];
    $globalOrder = 1;
    $leaves = [];

    foreach (array_values($definition['categories']) as $c => $item) {
      [$name, $registers] = is_array($item) ? $item : [$item, []];
      $number = $this->number($c, $catType);

      $category = Category::create([
        'archive_id' => $archive->id,
        'number' => $number,
        'name' => $name,
        'custom_id' => $this->customId($name),
        'numeral_type' => $catType,
        'custom_id_type' => 'auto',
        'order' => $c,
        'global_order' => $globalOrder++,
      ]);

      foreach (array_values($registers) as $r => $registerName) {
        $leaves[] = Category::create([
          'archive_id' => $archive->id,
          'parent_id' => $category->id,
          'number' => $number . '.' . $this->number($r, $regType, true),
          'name' => $registerName,
          'custom_id' => $this->customId($registerName),
          'numeral_type' => $regType,
          'custom_id_type' => 'auto',
          'order' => $r,
          'global_order' => $globalOrder++,
        ]);
      }

      if (empty($registers)) {
        $leaves[] = $category;
      }
    }

    return $leaves;
  }

  private function number(int $index, string $type, bool $register = false): string
  {
    return match ($type) {
      'alpha' => $register ? strtolower(chr(65 + $index)) : chr(65 + $index),
      'roman' => $this->roman($index + 1),
      default => (string) ($index + 1),
    };
  }

  private function roman(int $num): string
  {
    $map = ['M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1];
    $out = '';
    foreach ($map as $letter => $value) {
      while ($num >= $value) {
        $out .= $letter;
        $num -= $value;
      }
    }
    return $out;
  }

  /** Same as the editor's auto id: first two letters, uppercase */
  private function customId(string $name): string
  {
    return mb_strtoupper(mb_substr(Str::ascii($name), 0, 2));
  }

  /** A field value: a closure, or a random entry of a list */
  private function value(callable|array $source): string
  {
    return is_callable($source) ? (string) $source() : $source[array_rand($source)];
  }

  // ---------------------------------------------------------------------------
  // Images
  // ---------------------------------------------------------------------------

  /** Copy a pool image into the archive folder and create its media row */
  private function attachImage(Archive|Record $mediable, Archive $archive, int $position = 0): void
  {
    $image = $this->pool[array_rand($this->pool)];
    $base = 'bild-' . Str::lower(Str::random(6)) . '_' . uniqid();
    $dir = Storage::disk('public')->path('archives/' . $archive->uuid);
    File::ensureDirectoryExists($dir);

    $original = "{$base}_original.jpg";
    $resized = "{$base}_resized.jpg";
    File::copy($image['original'], "{$dir}/{$original}");
    File::copy($image['resized'], "{$dir}/{$resized}");

    $media = new Media([
      'name' => "IMG_" . mt_rand(1000, 9999) . '.jpg',
      'original_name' => $original,
      'resized_name' => $resized,
      'resized_width' => $image['width'],
      'resized_height' => $image['height'],
      'aspect_ratio' => $image['aspect_ratio'],
      'mime_type' => $image['mime_type'],
      'size' => $image['size'],
      'position' => $position,
    ]);
    $media->mediable()->associate($mediable);
    $media->save();
  }

  /** Download (or generate) a set of images once, with resized versions */
  private function preparePool(): void
  {
    $dir = storage_path('app/' . self::POOL_DIR);
    File::ensureDirectoryExists($dir);

    $sizes = [[1600, 1200], [1200, 1600], [1400, 1400], [1800, 1100], [1100, 1500]];

    for ($i = 1; $i <= self::POOL_SIZE; $i++) {
      $original = "{$dir}/{$i}_original.jpg";
      $resized = "{$dir}/{$i}_resized.jpg";
      [$w, $h] = $sizes[$i % count($sizes)];

      if (!File::exists($original)) {
        $this->download("https://picsum.photos/seed/kartei-{$i}/{$w}/{$h}", $original)
          || $this->placeholder($original, $w, $h, $i);
      }

      $data = File::exists($resized)
        ? $this->dimensions($resized)
        : (new ResizeImageAction())->execute($original, $resized);

      $this->pool[] = [
        'original' => $original,
        'resized' => $resized,
        'width' => $data['width'],
        'height' => $data['height'],
        'aspect_ratio' => $data['aspect_ratio'],
        'mime_type' => 'image/jpeg',
        'size' => File::size($original),
      ];
    }
  }

  private function download(string $url, string $path): bool
  {
    try {
      $response = Http::timeout(15)->get($url);
      if ($response->successful() && str_starts_with($response->header('Content-Type'), 'image/')) {
        File::put($path, $response->body());
        return true;
      }
    } catch (\Throwable) {
      // fall back to a placeholder
    }
    return false;
  }

  private function placeholder(string $path, int $w, int $h, int $seed): void
  {
    $img = imagecreatetruecolor($w, $h);
    $hue = ($seed * 47) % 360;
    for ($y = 0; $y < $h; $y++) {
      [$r, $g, $b] = $this->hsl($hue + 40 * $y / $h, 0.35, 0.35 + 0.3 * $y / $h);
      imageline($img, 0, $y, $w, $y, imagecolorallocate($img, $r, $g, $b));
    }
    imagestring($img, 5, 40, 40, "Kartei Demo #{$seed}", imagecolorallocate($img, 255, 255, 255));
    imagejpeg($img, $path, 85);
    imagedestroy($img);
  }

  private function hsl(float $h, float $s, float $l): array
  {
    $h = fmod($h, 360) / 360;
    $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
    $p = 2 * $l - $q;
    $f = function ($t) use ($p, $q) {
      $t = $t < 0 ? $t + 1 : ($t > 1 ? $t - 1 : $t);
      return match (true) {
        $t < 1 / 6 => $p + ($q - $p) * 6 * $t,
        $t < 1 / 2 => $q,
        $t < 2 / 3 => $p + ($q - $p) * (2 / 3 - $t) * 6,
        default => $p,
      };
    };
    return [(int) ($f($h + 1 / 3) * 255), (int) ($f($h) * 255), (int) ($f($h - 1 / 3) * 255)];
  }

  private function dimensions(string $path): array
  {
    [$w, $h] = getimagesize($path);
    return ['width' => $w, 'height' => $h, 'aspect_ratio' => $w > $h ? 'landscape' : ($w < $h ? 'portrait' : 'square')];
  }

  // ---------------------------------------------------------------------------
  // Archive definitions
  // ---------------------------------------------------------------------------

  private function archives(): array
  {
    $year = fn (int $from, int $to) => fn () => (string) mt_rand($from, $to);
    $date = fn (int $from, int $to) => fn () => sprintf('%02d.%02d.%d', mt_rand(1, 28), mt_rand(1, 12), mt_rand($from, $to));
    $cm = fn (int $min, int $max) => fn () => mt_rand($min, $max) . ' × ' . mt_rand($min, $max) . ' cm';
    $chf = fn (int $min, int $max) => fn () => 'CHF ' . number_format(mt_rand($min, $max) * 10, 0, '.', '\'');
    $places = ['Zürich', 'Bern', 'Basel', 'Luzern', 'St. Gallen', 'Winterthur', 'Chur', 'Lugano', 'Genf', 'Lausanne', 'Thun', 'Schaffhausen'];
    $states = ['sehr gut', 'gut', 'mittel', 'beschädigt', 'restauriert'];

    return [
      [
        'name' => 'Fotoarchiv Zürich',
        'acronym' => 'FZ',
        'numerals' => ['decimal', 'decimal'],
        'categories' => [
          ['Stadtansichten', ['Altstadt', 'Seebecken', 'Industriequartier', 'Aussenquartiere']],
          ['Ereignisse', ['Sechseläuten', 'Street Parade', 'Züri Fäscht']],
          ['Verkehr', ['Tram', 'Bahnhof', 'Flughafen']],
          'Porträts',
        ],
        'fields' => [
          'Titel' => ['Blick vom Lindenhof', 'Limmatquai am Abend', 'Bellevue im Winter', 'Hauptbahnhof Halle', 'Quaibrücke', 'Prime Tower im Nebel', 'Markt am Bürkliplatz', 'Badi Utoquai', 'Grossmünster', 'Langstrasse'],
          'Jahr' => $year(1890, 2024),
          'Fotograf/in' => ['Hans Baumann', 'Elsa Frei', 'Walter Mittelholzer', 'Gertrud Vogler', 'Peter Ammon', 'unbekannt'],
          'Negativnummer' => fn () => 'N-' . mt_rand(10000, 99999),
        ],
        'tags' => ['Schwarzweiss', 'Farbe', 'Luftaufnahme', 'Nacht', 'Winter', 'Menschen', 'Architektur'],
      ],
      [
        'name' => 'Plakatsammlung',
        'acronym' => 'PS',
        'numerals' => ['alpha', 'alpha'],
        'categories' => [
          ['Tourismus', ['Berge', 'Seen', 'Städte']],
          ['Kultur', ['Theater', 'Konzert', 'Ausstellung', 'Film']],
          ['Politik', ['Abstimmungen', 'Wahlen']],
          ['Produktwerbung', ['Lebensmittel', 'Mode', 'Technik']],
        ],
        'fields' => [
          'Titel' => ['Winterferien in Graubünden', 'Kunsthaus Zürich', 'Ja zur AHV', 'Ovomaltine', 'Montreux Jazz', 'Engadin', 'Stimmt Nein', 'Schweizer Käse', 'Swissair', 'Zirkus Knie'],
          'Gestalter/in' => ['Herbert Leupin', 'Josef Müller-Brockmann', 'Niklaus Stoecklin', 'Celestino Piatti', 'Donald Brun', 'Emil Cardinaux'],
          'Jahr' => $year(1905, 1995),
          'Format' => ['F4 (90.5 × 128 cm)', 'Weltformat', 'A1', 'A2', '70 × 100 cm'],
          'Druckerei' => ['Wolfensberger', 'J.E. Wolfensberger AG', 'Orell Füssli', 'Lithographie Bern'],
        ],
        'tags' => ['Lithografie', 'Offset', 'Typografie', 'Illustration', 'Fotomontage', 'Rarität'],
      ],
      [
        'name' => 'Architekturbüro Projekte',
        'acronym' => 'AB',
        'numerals' => ['roman', 'decimal'],
        'categories' => [
          ['Wohnbau', ['Einfamilienhäuser', 'Mehrfamilienhäuser', 'Umbauten']],
          ['Öffentliche Bauten', ['Schulen', 'Sporthallen']],
          ['Wettbewerbe', ['Gewonnen', 'Nicht prämiert']],
          'Studien',
        ],
        'fields' => [
          'Projekt' => ['Haus am Hang', 'Siedlung Sonnenrain', 'Schulhaus Buchholz', 'Umbau Villa Rosa', 'Turnhalle Riet', 'Wohnen am Bach', 'Ersatzneubau Lindenstrasse', 'Atelierhaus'],
          'Ort' => $places,
          'Bauherrschaft' => ['Privat', 'Stadt Zürich', 'Genossenschaft ABZ', 'Gemeinde Thalwil', 'Kanton Bern', 'Stiftung Wohnen'],
          'Fertigstellung' => $year(1998, 2026),
          'Phase' => ['Vorprojekt', 'Bauprojekt', 'Ausführung', 'Abgeschlossen'],
        ],
        'tags' => ['Holzbau', 'Beton', 'Minergie', 'Denkmalpflege', 'Modell', 'Plan', 'Baustelle'],
      ],
      [
        'name' => 'Herbarium Alpenflora',
        'acronym' => 'HA',
        'numerals' => ['decimal', 'alpha'],
        'categories' => [
          ['Blütenpflanzen', ['Enziangewächse', 'Hahnenfussgewächse', 'Korbblütler', 'Orchideen']],
          ['Farne', ['Tüpfelfarne', 'Streifenfarne']],
          ['Gräser', ['Süssgräser', 'Sauergräser']],
          'Moose',
        ],
        'fields' => [
          'Art' => ['Gentiana acaulis', 'Leontopodium alpinum', 'Ranunculus glacialis', 'Cypripedium calceolus', 'Arnica montana', 'Soldanella alpina', 'Primula hirsuta', 'Silene acaulis'],
          'Fundort' => ['Furkapass', 'Grimsel', 'Val Müstair', 'Pilatus', 'Gemmipass', 'Albula', 'Säntis', 'Nationalpark'],
          'Höhe' => fn () => mt_rand(800, 3200) . ' m ü. M.',
          'Gesammelt am' => $date(1920, 2024),
          'Sammler/in' => ['A. Thellung', 'H. Christ', 'M. Rikli', 'E. Landolt', 'R. Keller'],
        ],
        'tags' => ['geschützt', 'selten', 'Typusbeleg', 'Hochalpin', 'Feuchtgebiet', 'Kalk', 'Silikat'],
      ],
      [
        'name' => 'Familienarchiv Brunner',
        'acronym' => 'FB',
        'numerals' => ['decimal', 'decimal'],
        'categories' => [
          ['Fotos', ['Hochzeiten', 'Ferien', 'Feste', 'Alltag']],
          ['Dokumente', ['Urkunden', 'Briefe', 'Zeugnisse']],
          'Erbstücke',
        ],
        'fields' => [
          'Beschreibung' => ['Hochzeit von Anna und Ernst', 'Ferien im Tessin', 'Taufe von Lisa', 'Geburtstag Grossvater', 'Familienfoto vor dem Haus', 'Brief aus Amerika', 'Militärdienstbüchlein', 'Taschenuhr von Urgrossvater'],
          'Datum' => $date(1900, 2010),
          'Personen' => ['Anna, Ernst', 'Lisa, Max, Rosa', 'die ganze Familie', 'Grossvater Hans', 'Tante Margrit', 'Onkel Fritz, Peter'],
          'Ort' => $places,
        ],
        'tags' => ['Grosseltern', 'Kinder', 'Ausgewandert', 'Bauernhof', 'Ferien', 'Feier'],
      ],
      [
        'name' => 'Kunstsammlung Meier',
        'acronym' => 'KM',
        'numerals' => ['roman', 'roman'],
        'categories' => [
          ['Malerei', ['Öl', 'Aquarell', 'Acryl']],
          ['Grafik', ['Radierung', 'Holzschnitt', 'Siebdruck']],
          ['Skulptur', ['Bronze', 'Holz']],
          'Fotografie',
        ],
        'fields' => [
          'Titel' => ['Ohne Titel', 'Komposition in Blau', 'Landschaft bei Ascona', 'Stillleben mit Krug', 'Bildnis einer Frau', 'Rhythmus II', 'Bergsee', 'Figur, stehend'],
          'Künstler/in' => ['Sophie Taeuber-Arp', 'Ferdinand Hodler', 'Cuno Amiet', 'Meret Oppenheim', 'Max Bill', 'Giovanni Giacometti', 'Verena Loewensberg'],
          'Jahr' => $year(1890, 2020),
          'Masse' => $cm(20, 180),
          'Versicherungswert' => $chf(200, 50000),
          'Standort' => ['Wohnzimmer', 'Depot', 'Büro', 'Leihgabe Kunstmuseum', 'Treppenhaus'],
        ],
        'tags' => ['signiert', 'gerahmt', 'Leihgabe', 'Restaurierung nötig', 'Provenienz geklärt', 'Edition'],
      ],
      [
        'name' => 'Schallplatten',
        'acronym' => 'SP',
        'numerals' => ['alpha', 'decimal'],
        'categories' => [
          ['Jazz', ['Bebop', 'Cool Jazz', 'Free Jazz']],
          ['Rock', ['Classic Rock', 'Punk', 'Indie']],
          ['Klassik', ['Barock', 'Romantik', 'Moderne']],
          ['Elektronik', ['Techno', 'Ambient']],
          'Volksmusik',
        ],
        'fields' => [
          'Album' => ['Kind of Blue', 'Blue Train', 'London Calling', 'Unknown Pleasures', 'Goldberg-Variationen', 'Music for Airports', 'Selected Ambient Works', 'Mani Matter Live', 'Horses', 'Time Out'],
          'Interpret/in' => ['Miles Davis', 'John Coltrane', 'The Clash', 'Joy Division', 'Glenn Gould', 'Brian Eno', 'Aphex Twin', 'Mani Matter', 'Patti Smith', 'Dave Brubeck'],
          'Label' => ['Columbia', 'Blue Note', 'Factory', 'CBS', 'Warp', 'Zytglogge', 'Arista'],
          'Jahr' => $year(1955, 2023),
          'Zustand' => ['Mint', 'Near Mint', 'VG+', 'VG', 'Good'],
        ],
        'tags' => ['Erstpressung', 'signiert', 'Gatefold', 'Farbiges Vinyl', 'Import', 'Doppel-LP'],
      ],
      [
        'name' => 'Reisefotografie',
        'acronym' => 'RF',
        'numerals' => ['decimal', 'decimal'],
        'categories' => [
          ['Europa', ['Skandinavien', 'Italien', 'Portugal', 'Island']],
          ['Asien', ['Japan', 'Vietnam', 'Indien']],
          ['Amerika', ['Kanada', 'Patagonien', 'Mexiko']],
          ['Afrika', ['Marokko', 'Namibia']],
        ],
        'fields' => [
          'Motiv' => ['Fjord bei Sonnenaufgang', 'Strassenmarkt', 'Tempel im Nebel', 'Küstenstrasse', 'Wüstendünen', 'Gletscherlagune', 'Altstadtgasse', 'Fischerboote', 'Reisfelder'],
          'Datum' => $date(2008, 2026),
          'Kamera' => ['Fujifilm X-T4', 'Leica M6', 'Sony A7 III', 'Ricoh GR III', 'Nikon F3', 'iPhone 15 Pro'],
          'Objektiv' => ['23 mm f/2', '35 mm f/1.4', '50 mm f/2', '24–70 mm f/2.8', '90 mm f/2.8'],
        ],
        'tags' => ['Landschaft', 'Street', 'Analog', 'Panorama', 'Goldene Stunde', 'Tiere', 'Portfolio'],
      ],
      [
        'name' => 'Museum für Handwerk',
        'acronym' => 'MH',
        'numerals' => ['roman', 'alpha'],
        'categories' => [
          ['Holzbearbeitung', ['Hobel', 'Sägen', 'Stemmeisen']],
          ['Metallbearbeitung', ['Schmiedewerkzeug', 'Feilen']],
          ['Textil', ['Spinnräder', 'Webstühle']],
          ['Landwirtschaft', ['Sensen', 'Milchwirtschaft']],
        ],
        'fields' => [
          'Objekt' => ['Schlichthobel', 'Gestellsäge', 'Hufeisenzange', 'Spinnrad', 'Butterfass', 'Dengelstock', 'Zimmermannsbeil', 'Wollkarde', 'Melkschemel'],
          'Herkunft' => ['Emmental', 'Toggenburg', 'Appenzell', 'Wallis', 'Jura', 'Bündner Oberland', 'Entlebuch'],
          'Datierung' => fn () => 'um ' . (mt_rand(170, 195) * 10),
          'Material' => ['Buche', 'Eiche', 'Esche, Eisen', 'Eisen geschmiedet', 'Nussbaum', 'Fichte, Leder'],
          'Zustand' => $states,
          'Inventarnummer' => fn () => 'MH ' . mt_rand(1950, 2024) . '.' . mt_rand(1, 999),
        ],
        'tags' => ['Schenkung', 'Ankauf', 'ausgestellt', 'Depot', 'Gebrauchsspuren', 'Hersteller bekannt'],
      ],
      [
        'name' => 'Bibliothek Raritäten',
        'acronym' => 'BR',
        'numerals' => ['alpha', 'roman'],
        'categories' => [
          ['Handschriften', ['Mittelalter', 'Neuzeit']],
          ['Frühdrucke', ['Inkunabeln', '16. Jahrhundert']],
          ['Karten und Atlanten', ['Schweiz', 'Welt']],
          'Künstlerbücher',
        ],
        'fields' => [
          'Titel' => ['Stundenbuch', 'Schweizer Chronik', 'Topographia Helvetiae', 'Kosmographie', 'Psalterium', 'Atlas Novus', 'Kräuterbuch', 'Totentanz'],
          'Autor/in' => ['Johannes Stumpf', 'Matthäus Merian', 'Sebastian Münster', 'Konrad Gessner', 'anonym', 'Aegidius Tschudi'],
          'Erscheinungsjahr' => $year(1450, 1980),
          'Signatur' => fn () => 'Rar ' . mt_rand(1, 40) . ' ' . chr(mt_rand(65, 90)) . ' ' . mt_rand(1, 300),
          'Zustand' => $states,
        ],
        'tags' => ['Pergament', 'koloriert', 'Erstausgabe', 'Provenienz', 'digitalisiert', 'Einband restauriert'],
      ],
    ];
  }
}
