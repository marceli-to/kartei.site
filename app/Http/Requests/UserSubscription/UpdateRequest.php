<?php
namespace App\Http\Requests\UserSubscription;
use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
  /**
  * Get the validation rules that apply to the request.
  *
  * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
  */
  public function rules(): array
  {
    return [
      'subscription' => ['required', 'exists:subscription_plans,uuid'],
      'payment_interval' => ['required'],
      'payment_method' => ['required'],
    ];
  }
}