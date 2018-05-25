<?php

namespace App\Http\Resources;

use App\Model\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Passport\Token;

/**
 * @mixin User
 */
class UserLoggedInResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array
     * @throws \RuntimeException
     */
    public function toArray($request)
    {
        /** @var Token $accessToken */
        $accessToken = $this
            ->tokens
            ->filter(function (Token $token) {
                return $token->name === User::ACCESS_TOKEN_KEY;
            })
            ->first();

        if ($accessToken === null) {
            throw new \RuntimeException('No main access token for user ' . $this->id);
        }

        return [
            'data' => [
                'id' => $this->id,
                'email' => $this->email,
                'status' => $this->status,
                'access_token' => $accessToken->id,
                'companies' => CompanyResource::collection($this->companies),
            ],
        ];
    }
}
