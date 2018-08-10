<?php
namespace App\Http\Resources;

use App\Models\MeasurementProfile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MeasurementProfile
 */
class MeasurementProfileResource extends JsonResource
{
    /**
     * @param  Request $request
     * @return array
     * @throws \InvalidArgumentException
     */
    public function toArray($request)
    {
        return [
            'data' => [
                'id' => $this->id,
                'type' => $this->type,
                'garment' => $this->garment,
                'created_at' => $this->created_at->toIso8601ZuluString(),
                'current_measurements' => MeasurementProfileMeasurementResource::collection($this->current_measurements),
                'current_name' => $this->current_name,
                'commits' => $this->relationLoaded('commits')
                    ? MeasurementProfileCommitResource::collection($this->commits)
                    : [],
            ],
        ];
    }
}
