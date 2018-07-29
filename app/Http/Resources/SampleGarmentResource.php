<?php
namespace App\Http\Resources;

use App\Models\SampleGarment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin SampleGarment
 */
final class SampleGarmentResource extends JsonResource
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
                'garment' => $this->garment,
                'created_at' => $this->created_at->toIso8601ZuluString(),
                'updated_at' => $this->updated_at->toIso8601ZuluString(),
                'deleted_at' => $this->deleted_at ? $this->deleted_at->toIso8601ZuluString() : null,
            ],
        ];
    }
}
