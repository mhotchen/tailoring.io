<?php
namespace App\Http\Resources;

use App\Model\CustomerNote;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin CustomerNote
 */
class CustomerNoteResource extends JsonResource
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
                'note' => $this->note,
                'created_at' => $this->created_at->toIso8601ZuluString(),
                'updated_at' => $this->updated_at->toIso8601ZuluString(),
            ],
        ];
    }
}
