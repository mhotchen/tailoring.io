<?php
namespace App\Http\Resources;

use App\Models\MeasurementProfileCommit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MeasurementProfileCommit
 */
class MeasurementProfileCommitResource extends JsonResource
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
                'name' => $this->name,
                'message' => $this->message,
                'sample_garment' => new SampleGarmentResource($this->sampleGarment),
                'revision' => $this->revision,
                'created_at' => $this->created_at->toIso8601ZuluString(),
                'measurements' => $this->relationLoaded('measurements')
                    ? MeasurementProfileMeasurementResource::collection($this->measurements)
                    : [],
            ],
        ];
    }
}
