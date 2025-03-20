<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\UserCompanyResource;
use App\Http\Requests\UserCompany\CreateRequest;
use App\Http\Requests\UserCompany\UpdateRequest;
use App\Actions\UserCompany\Get as GetUserCompanyAction;
use App\Actions\UserCompany\Create as CreateUserCompanyAction;
use App\Actions\UserCompany\Update as UpdateUserCompanyAction;
use App\Models\Company;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class UserCompanyController extends Controller
{
  public function get(Request $request): AnonymousResourceCollection
  {
    $this->authorize('viewAny', Company::class);
    $companies = (new GetUserCompanyAction())->execute(auth()->user());
    return UserCompanyResource::collection($companies);
  }

  public function create(CreateRequest $request): UserCompanyResource
  {
    $this->authorize('create', Company::class);
    $company = (new CreateUserCompanyAction())->execute($request->all());
    return new UserCompanyResource($company);
  }

  public function update(UpdateRequest $request, Company $company): UserCompanyResource
  {
    $this->authorize('update', $company);
    $company = (new UpdateUserCompanyAction())->execute($company, $request->all());
    return new UserCompanyResource($company);
  }
}