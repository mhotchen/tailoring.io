<?php
namespace App\Http\Controllers;

use App\Http\Requests\MeasurementCommitCreateRequest;
use App\Http\Resources\MeasurementProfileCommitResource;
use App\Models\Company;
use App\Models\Customer;
use App\Models\MeasurementProfile;
use App\Models\MeasurementProfileCommit;
use App\Models\MeasurementProfileMeasurement;
use Auth;
use DB;
use Illuminate\Http\JsonResponse;

final class MeasurementProfileController extends Controller
{
    /**
     * @param MeasurementCommitCreateRequest $measurementCommitCreateRequest
     * @param Company                        $company
     * @param string                         $customerId
     * @param string                         $profileId
     * @return MeasurementProfileCommitResource|JsonResponse
     * @throws \Illuminate\Database\Eloquent\MassAssignmentException
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Throwable
     */
    public function commit(
        MeasurementCommitCreateRequest $measurementCommitCreateRequest,
        Company $company,
        string $customerId,
        string $profileId
    ) {
        // Retrieving the profile like this might seem verbose but it ensures all the parameters are correctly related.

        /** @var Customer $customer */
        $customer = $company->customers()->findOrFail($customerId);
        /** @var MeasurementProfile $measurementProfile */
        $measurementProfile = $customer->measurementProfiles()->findOrFail($profileId);
        $createdBy = Auth::user();

        // Validate request after getting profile because the request validator depends on valid URL params.
        $request = $measurementCommitCreateRequest->validated();

        $commit = new MeasurementProfileCommit;
        $commit->fillFromRequest($request, $measurementProfile, $company, $createdBy);

        $measurements = collect($request['data']['measurements'])
            ->map(function (array $request) use ($company, $createdBy): MeasurementProfileMeasurement {
                $measurement = new MeasurementProfileMeasurement;
                $measurement->fillFromRequest($request, $company, $createdBy);

                return $measurement;
            })
            ->filter([$measurementProfile, 'filterMeasurement']);

        // No changes so there's no need for a new commit to be created. At the same time the request isn't invalid
        // so mark as 202 Accepted.
        if ($measurements->isEmpty() && $measurementProfile->current_name === $commit->name) {
            return JsonResponse::create(null, JsonResponse::HTTP_ACCEPTED);
        }

        DB::transaction(function () use ($measurementProfile, $commit, $measurements) {
            $measurementProfile->commits()->save($commit);
            $commit->measurements()->saveMany($measurements);
        });

        $commit->loadMissing(['measurements.setting']);

        return new MeasurementProfileCommitResource($commit);
    }
}
