<?php
namespace App\Actions\UserAddress;
use App\Models\Address;
use App\Models\User;

class Update
{
  public function execute(array $data, User $user, bool $isBilling = false): Address
  {
    // Set the billing flag in the data
    $data['is_billing'] = $isBilling;
    
    // Find or create the address using the polymorphic relationship
    $address = Address::firstOrCreate(
      [
        'addressable_id' => $user->id,
        'addressable_type' => User::class,
        'is_billing' => $isBilling
      ],
      $data
    );
    
    // If the address already existed, update its fields
    if (!$address->wasRecentlyCreated) {
      $address->fill($data);
      $address->save();
    }
    
    return $address;
  }
}