<?php
namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TemplateField;
use App\Actions\TemplateField\Delete as DeleteTemplateFieldAction;

class TemplateFieldController extends Controller
{
  /**
   * Delete a template field
   * 
   * @param TemplateField $templateField
   */
  public function destroy(TemplateField $templateField)
  {
    (new DeleteTemplateFieldAction())->execute($templateField);
    return response()->json([
      'success' => true
    ]);
  }
  
}
