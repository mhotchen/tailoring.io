<?php
namespace App\Http\Controllers;

use App\Http\Requests\SampleGarmentCreateRequest;
use App\Http\Requests\SampleGarmentUpdateRequest;
use App\Http\Resources\SampleGarmentResource;
use App\Models\Company;
use App\Models\SampleGarment;
use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

final class SampleGarmentController extends Controller
{
    /**
     * @param Company $company
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Company $company, Request $request): ResourceCollection
    {
        // TODO partial index on company_id/deleted_at where NULL
        return SampleGarmentResource::collection($company->sampleGarments()->whereNull('deleted_at')->get());
    }

    /**
     * @param SampleGarmentCreateRequest $sampleGarmentCreateRequest
     * @param Company                    $company
     * @return SampleGarmentResource
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     */
    public function create(
        SampleGarmentCreateRequest $sampleGarmentCreateRequest,
        Company $company
    ): SampleGarmentResource
    {
        $sampleGarment = new SampleGarment;
        $sampleGarment->hydrateFromRequest($sampleGarmentCreateRequest->validated(), Auth::user());
        $company->sampleGarments()->save($sampleGarment);

        return new SampleGarmentResource($sampleGarment);
    }

    /**
     * @param SampleGarmentUpdateRequest $request
     * @param Company                    $company
     * @param string                     $id
     * @return SampleGarmentResource
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function put(
        SampleGarmentUpdateRequest $request,
        Company $company,
        string $id
    ): SampleGarmentResource
    {
        /** @var SampleGarment $sampleGarment */
        $sampleGarment = $company->sampleGarments()->findOrFail($id);
        $sampleGarment->fill($request->validated()['data']);
        $sampleGarment->updatedBy()->associate(Auth::user());
        $company->sampleGarments()->save($sampleGarment);

        return new SampleGarmentResource($sampleGarment);
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
        /** @var SampleGarment $sampleGarment */
        $sampleGarment = $company->sampleGarments()->findOrFail($id);
        $sampleGarment->softDelete(Auth::user());

        return JsonResponse::create(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
