<?php
namespace App\Http\Controllers;

use App\Http\Resources\MeasurementSettingResource;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class MeasurementSettingController extends Controller
{
    /**
     * @param Company $company
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Company $company, Request $request): ResourceCollection
    {
        // TODO partial index on deleted_at where NULL
        return MeasurementSettingResource::collection($company->measurementSettings()->whereNull('deleted_at')->get());
    }
}
