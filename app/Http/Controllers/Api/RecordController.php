<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Models\Record;
use App\Models\Archive;
use App\Http\Resources\RecordResource;
use App\Actions\Record\Get as GetRecordAction;
// use App\Actions\Record\Find as FindRecordAction;
// use App\Actions\Record\Create as CreateRecordAction;
// use App\Actions\Record\Update as UpdateRecordAction;
// use App\Actions\Record\Delete as DeleteRecordAction;
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
    $records = (new GetRecordAction())->execute(['archive_id' => $archive->id]);
    return response()->json(
      RecordResource::collection($records)
    );
  }
}
