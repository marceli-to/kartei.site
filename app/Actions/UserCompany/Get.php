<?php
namespace App\Actions\UserCompany;

class Get
{
  public function execute($user)
  {
    return $user->companies;
  }
}