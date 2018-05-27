<?php

namespace App\Http\Resources;

use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class UserLoggedInResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' => [
                'id' => $this->id,
                'email' => $this->email,
                'status' => $this->status,
                'companies' => CompanyResource::collection($this->companies),
            ],
        ];
    }
}
