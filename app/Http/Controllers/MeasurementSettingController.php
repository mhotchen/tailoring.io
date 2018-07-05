<?php
namespace App\Http\Controllers;

use App\Http\Resources\MeasurementSettingResource;
use App\Models\Company;
use App\Models\MeasurementSetting;
use Auth;
use Illuminate\Http\JsonResponse;
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

    /**
     * @param Company $company
     * @param string  $id
     * @return JsonResponse
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \InvalidArgumentException
     */
    public function delete(Company $company, string $id): JsonResponse
    {
        /** @var MeasurementSetting $measurementSetting */
        $measurementSetting = $company->measurementSettings()->findOrFail($id);
        $measurementSetting->softDelete(Auth::user());

        return JsonResponse::create(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
