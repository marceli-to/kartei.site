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
use App\Http\Requests\Record\StoreRequest;

class RecordController extends Controller
{
  /**
   * Get records for a specific archive
   *
   * @param Archive $archive
   * @return JsonResponse
   */
  public function get(Archive $archive): JsonResponse
  {
    return response()->json(
      RecordResource::collection(
        (new GetRecordAction())->execute(['archive_id' => $archive->id])
      )
    );
  }

  /**
   * Get a specific record
   * 
   * @param Record $record
   * @return JsonResponse
   */
  
  public function find(Record $record): JsonResponse
  {
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
    $record = (new UpdateRecordAction())->execute($record, $request->all());
    return response()->json(
      RecordResource::make($record)
    );
  }
}
