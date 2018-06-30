<?php
namespace App\Http\Controllers;

use App\Http\Requests\CompanyCreateRequest;
use App\Http\Requests\CompanyUpdateRequest;
use App\Http\Resources\CompanyResource;
use App\Mail\UserVerifyEmail;
use App\Measurement\Settings\DefaultMeasurementSetting;
use App\Measurement\Settings\DefaultMeasurementSettings;
use App\Models\Company;
use App\Models\MeasurementSetting;
use App\Models\User;
use App\Spa\UrlGenerator;
use Auth;
use DB;
use Illuminate\Support\Collection;
use Mail;

final class CompanyController extends Controller
{
    /** @var DefaultMeasurementSettings */
    private $defaultMeasurementSettings;

    public function __construct(DefaultMeasurementSettings $defaultMeasurementSettings)
    {
        $this->defaultMeasurementSettings = $defaultMeasurementSettings;
    }

    /**
     * @param  CompanyCreateRequest $request
     * @param  UrlGenerator         $urlGenerator
     * @return CompanyResource
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \InvalidArgumentException
     * @throws \Throwable
     */
    public function create(CompanyCreateRequest $request, UrlGenerator $urlGenerator): CompanyResource
    {
        $validatedRequest = $request->validated();
        $company = Company::fromRequest($validatedRequest);
        $user = User::fromRequest($validatedRequest['data']['users'][0]);

        DB::transaction(function () use ($company, $user): void {
            $company->save();
            $company->users()->save($user);
            $company->measurementSettings()->saveMany(
                (new Collection($this->defaultMeasurementSettings->getSettings()))
                    ->map(function (DefaultMeasurementSetting $default) use ($user) {
                        $setting = new MeasurementSetting;
                        $setting->fillFromDefault($default, $user);
                        return $setting;
                    })
            );
        });

        Mail::to($user->email)
            ->send(new UserVerifyEmail($user, $urlGenerator));

        return new CompanyResource($company);
    }

    /**
     * @param CompanyUpdateRequest $request
     * @param Company              $company
     * @return CompanyResource
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Throwable
     */
    public function update(CompanyUpdateRequest $request, Company $company): CompanyResource
    {
        $company->fill($request->validated()['data']);
        DB::transaction(function () use ($company) {
            if ($company->isDirty(['unit_of_measurement'])) {
                $company->roundMeasurementSettingsToUnitOfMeasurement(Auth::user());
            }

            $company->save();
        });
        return new CompanyResource($company);
    }
}
