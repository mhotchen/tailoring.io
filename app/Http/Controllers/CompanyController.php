<?php

namespace App\Http\Controllers;

use App\Http\Requests\CompanyCreate;
use App\Http\Resources\CompanyResource;
use App\Mail\UserVerifyEmail;
use App\Model\Company;
use App\Model\User;
use App\Spa\UrlGenerator;
use DB;
use Mail;

final class CompanyController extends Controller
{
    /**
     * @param  CompanyCreate $request
     * @param  UrlGenerator  $urlGenerator
     * @return CompanyResource
     * @throws \BadMethodCallException
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \InvalidArgumentException
     * @throws \Throwable
     */
    public function create(CompanyCreate $request, UrlGenerator $urlGenerator): CompanyResource
    {
        $validatedRequest = $request->validated();
        $company = Company::fromRequest($validatedRequest);
        $user = User::fromRequest($validatedRequest['data']['users'][0]);

        DB::transaction(function () use ($company, $user): void {
            $company->save();
            $company->users()->save($user);
        });

        Mail::to($user->email)
            ->send(new UserVerifyEmail($user, $urlGenerator));

        return new CompanyResource($company);
    }
}
