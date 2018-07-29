<?php
namespace App\Http\Resources;

use App\Models\MeasurementSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin MeasurementSetting
 */
final class MeasurementSettingResource extends JsonResource
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
                'type' => $this->type,
                'garments' => $this->garments,
                'min_value' => $this->min_value,
                'max_value' => $this->max_value,
                'created_at' => $this->created_at->toIso8601ZuluString(),
                'updated_at' => $this->updated_at->toIso8601ZuluString(),
                'deleted_at' => $this->deleted_at ? $this->deleted_at->toIso8601ZuluString() : null,
            ],
        ];
    }
}
