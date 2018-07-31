<?php
namespace App\Http\Resources;

use App\Models\MeasurementProfileMeasurement;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MeasurementProfileMeasurement
 */
class MeasurementProfileMeasurementResource extends JsonResource
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
                'setting' => new MeasurementSettingResource($this->setting),
                'value' => $this->value,
                'comment' => $this->comment,
                'created_at' => $this->created_at->toIso8601ZuluString(),
            ],
        ];
    }
}
