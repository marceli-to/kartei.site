<?php
namespace App\Actions\Archive;
use App\Models\Archive;
use App\Models\User;

class Get
{
  /**
   * Get archives based on specified options
   * 
   * @param array $options
   *        - 'user_id': Get archives for a specific user ID
   *        - 'admin': Get archives for the authenticated admin
   *        - 'all': Get all archives (requires Super Admin role)
   * @return \Illuminate\Database\Eloquent\Collection
   */
  public function execute(array $options = [])
  {
    $user = auth()->user();
    $query = Archive::query();
    
    // Get all archives (Super Admin only)
    if (!empty($options['all']) && $user->hasRole('Super Admin')) {
      return $query->get();
    }
    
    // Get archives for a specific user ID
    if (!empty($options['user_id'])) {
      // Only Super Admin can view other users' archives
      if ($options['user_id'] != $user->id && !$user->hasRole('Super Admin')) {
        // Return empty collection or handle unauthorized access
        return collect();
      }
      
      return $query->whereHas('users', function($q) use ($options) {
        $q->where('user_id', $options['user_id']);
      })->get();
    }
    
    // Get archives for the authenticated admin
    if (!empty($options['admin'])) {
      return $query->whereHas('users', function($q) use ($user) {
        $q->where('user_id', $user->id);
      })->get();
    }
    
    // Default behavior: get user's archives or all for Super Admin
    if ($user->hasRole('Super Admin')) {
      // Super Admin sees all archives by default
      return $query->get();
    } 
    else {
      // Regular users see only their archives
      return $query->whereHas('users', function($q) use ($user) {
        $q->where('user_id', $user->id);
      })->get();
    }
  }
}