<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Record;
use App\Models\Archive;
use App\Http\Resources\RecordResource;
use App\Actions\Record\Get as GetRecordAction;
use App\Actions\Record\Find as FindRecordAction;
use App\Actions\Record\Create as CreateRecordAction;
use App\Actions\Record\Update as UpdateRecordAction;
use App\Actions\Record\Delete as DeleteRecordAction;
use App\Http\Requests\Record\StoreRequest;
use App\Http\Requests\Record\IndexRequest;

class RecordController extends Controller
{
  /**
   * One page of an archive's records, filtered and sorted
   */
  public function get(IndexRequest $request, Archive $archive): JsonResponse
  {
    $this->authorize('view', $archive);
    $result = (new GetRecordAction())->execute($archive, $request->validated());

    return response()->json([
      'data' => RecordResource::collection($result['records'])->resolve($request),
      'meta' => [
        'current_page' => $result['page'],
        'last_page' => $result['last_page'],
        'per_page' => $result['per_page'],
        'total' => $result['total'],
        // All records of the archive, regardless of filters
        'archive_total' => $result['archive_total'],
      ],
    ]);
  }

  /**
   * Get a specific record
   * 
   * @param Record $record
   * @return JsonResponse
   */
  
  public function find(Record $record): JsonResponse
  {
    $this->authorize('view', $record->archive);
    return response()->json(
      RecordResource::make(
        (new FindRecordAction())->execute($record)
      )
    );
  }

  /**
   * Create a new record
   *
   * @param StoreRequest $request
   * @return JsonResponse
   */
  public function create(StoreRequest $request): JsonResponse
  {
    $archive = Archive::where('uuid', $request->input('archive_id'))->firstOrFail();
    $this->authorize('createRecord', $archive);
    $record = (new CreateRecordAction())->execute($request->all());
    return response()->json(
      RecordResource::make($record)
    );
  }

  /**
   * Update an existing record
   * 
   * @param Record $record
   * @param Request $request
   * @return JsonResponse
   */
  public function update(Record $record, Request $request): JsonResponse
  {
    $this->authorize('updateRecord', $record->archive);
    // A record can't be moved to another archive
    $record = (new UpdateRecordAction())->execute($record, $request->except(['archive_id', 'uuid']));
    return response()->json(
      RecordResource::make($record)
    );
  }

  /**
   * Delete a record
   *
   * @param Record $record
   * @return JsonResponse
   */
  public function destroy(Record $record): JsonResponse
  {
    $this->authorize('deleteRecord', $record->archive);
    (new DeleteRecordAction())->execute($record);
    return response()->json([
      'success' => true
    ]);
  }
}
