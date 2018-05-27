<?php
namespace App\Http\Resources;

use App\Model\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Customer
 */
class CustomerResource extends JsonResource
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
                'email' => $this->email,
                'telephone' => $this->telephone,
                'created_at' => $this->created_at->toIso8601ZuluString(),
                'updated_at' => $this->updated_at->toIso8601ZuluString(),
                'notes' => CustomerNoteResource::collection($this->notes),
            ],
        ];
    }
}
