<?php
namespace App\Actions\Record;
use App\Models\Archive;
use App\Models\Category;
use App\Models\Record;
use App\Models\RecordField;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * One page of an archive's records, filtered and sorted on the server.
 *
 * Display numbers are positional (they depend on all records of a category),
 * so filtering and sorting run on lightweight rows (id, category, dates) for
 * the whole archive. Only the records of the requested page are loaded with
 * their fields, tags and images, which is the expensive part.
 */
class Get
{
  public const SORT_ORDERS = ['name', 'date', 'created_at', 'updated_at'];

  /**
   * @param array{search?: ?string, sort?: ?string, categories?: array, registers?: array, tags?: array, page?: int, per_page?: int} $params
   * @return array{records: Collection, total: int, archive_total: int, page: int, per_page: int, last_page: int}
   */
  public function execute(Archive $archive, array $params = []): array
  {
    $perPage = (int) ($params['per_page'] ?? 48);
    $page = max(1, (int) ($params['page'] ?? 1));

    $categories = Category::with('parent')->where('archive_id', $archive->id)->get()->keyBy('id');

    // All records of the archive in display-number order within each category
    $rows = Record::where('archive_id', $archive->id)
      ->orderBy('category_id')
      ->orderBy('created_at')
      ->orderBy('id')
      ->toBase()
      ->get(['id', 'category_id', 'created_at', 'updated_at']);

    $counters = [];
    foreach ($rows as $row) {
      $row->index = $counters[$row->category_id] = ($counters[$row->category_id] ?? -1) + 1;
    }
    $archiveTotal = $rows->count();

    $rows = $this->filter($rows, $archive, $categories, $params);
    $rows = $this->sort($rows, $categories, $params['sort'] ?? 'name');

    $total = $rows->count();
    $pageRows = $rows->slice(($page - 1) * $perPage, $perPage)->values();

    $records = Record::with('category.parent', 'tags', 'media', 'fields', 'archive')
      ->whereIn('id', $pageRows->pluck('id'))
      ->get()
      ->keyBy('id');

    $pageRecords = $pageRows
      ->filter(fn ($row) => $records->has($row->id))
      ->map(fn ($row) => $records[$row->id]->setDisplayIndex($row->index))
      ->values();

    return [
      'records' => $pageRecords,
      'total' => $total,
      'archive_total' => $archiveTotal,
      'page' => $page,
      'per_page' => $perPage,
      'last_page' => max(1, (int) ceil($total / $perPage)),
    ];
  }

  private function filter(Collection $rows, Archive $archive, Collection $categories, array $params): Collection
  {
    // A category includes its registers. Picking registers of a selected category
    // narrows it down to them (category → register → result); registers whose
    // category isn't selected are added on their own.
    $selectedCategories = array_filter((array) ($params['categories'] ?? []));
    $selectedRegisters = array_filter((array) ($params['registers'] ?? []));
    if ($selectedCategories || $selectedRegisters) {
      $narrowed = $categories
        ->filter(fn ($category) => $category->parent && in_array($category->uuid, $selectedRegisters, true))
        ->map(fn ($register) => $register->parent->uuid)
        ->unique()
        ->all();
      $allowed = $categories
        ->filter(function ($category) use ($selectedCategories, $selectedRegisters, $narrowed) {
          if (in_array($category->uuid, $selectedRegisters, true)) return true;
          $categoryUuid = $category->parent?->uuid ?? $category->uuid;
          return in_array($categoryUuid, $selectedCategories, true)
            && !in_array($categoryUuid, $narrowed, true);
        })
        ->keys()
        ->flip();
      $rows = $rows->filter(fn ($row) => $allowed->has($row->category_id));
    }

    // Any of the selected tags
    $selectedTags = array_filter((array) ($params['tags'] ?? []));
    if ($selectedTags) {
      $tagged = DB::table('record_tag')
        ->join('tags', 'tags.id', '=', 'record_tag.tag_id')
        ->where('tags.archive_id', $archive->id)
        ->whereIn('tags.uuid', $selectedTags)
        ->distinct()
        ->pluck('record_tag.record_id')
        ->flip();
      $rows = $rows->filter(fn ($row) => $tagged->has($row->id));
    }

    // Display number or any field content, case-insensitive
    $search = trim((string) ($params['search'] ?? ''));
    if ($search !== '') {
      // "!" as escape character: a backslash is read differently by MySQL and SQLite
      $like = '%' . str_replace(['!', '%', '_'], ['!!', '!%', '!_'], mb_strtolower($search)) . '%';
      $matchingFields = RecordField::whereIn('record_id', $rows->pluck('id'))
        ->whereRaw("LOWER(content) LIKE ? ESCAPE '!'", [$like])
        ->distinct()
        ->pluck('record_id')
        ->flip();

      $needle = mb_strtolower($search);
      $rows = $rows->filter(function ($row) use ($matchingFields, $categories, $archive, $needle) {
        if ($matchingFields->has($row->id)) {
          return true;
        }
        $category = $categories[$row->category_id] ?? null;
        return $category
          && str_contains(mb_strtolower(Record::formatDisplayNumber($category, $archive, $row->index)), $needle);
      });
    }

    return $rows;
  }

  private function sort(Collection $rows, Collection $categories, string $sort): Collection
  {
    $sorted = match ($sort) {
      // Newest first
      'date', 'created_at' => $rows->sortBy([['created_at', 'desc'], ['id', 'desc']]),
      'updated_at' => $rows->sortBy([['updated_at', 'desc'], ['id', 'desc']]),
      // Archive order: category tree order, then card number
      default => $rows->sortBy([
        fn ($a, $b) => ($categories[$a->category_id]->global_order ?? 0) <=> ($categories[$b->category_id]->global_order ?? 0),
        fn ($a, $b) => $a->category_id <=> $b->category_id,
        fn ($a, $b) => $a->index <=> $b->index,
      ]),
    };

    return $sorted->values();
  }
}
