<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserAttemptLogin;
use App\Http\Requests\UserVerifyEmail;
use App\Http\Resources\UserLoggedInResource;
use App\Model\User;
use Hash;
use Illuminate\Http\JsonResponse;
use Log;
use Symfony\Component\HttpFoundation\Response;

final class UserController extends Controller
{
    /**
     * Don't rely on validators to verify the email/password because they will leak information about which field
     * is invalid. Do the hard work in the controller so we can use the same error type for all credential problems.
     *
     * Annoyingly resources implement a Responsable interface whereas JsonResponse extends the Symfony Response class
     * so we can't do type hinting on the response of this method.
     *
     * @param UserAttemptLogin $request
     * @return UserLoggedInResource|JsonResponse
     * @throws \InvalidArgumentException
     */
    public function attemptLogin(UserAttemptLogin $request)
    {
        $validatedRequest = $request->validated();

        /*
         * Make brute force impractical.
         */
        sleep(1);

        $user = User::whereEmail($validatedRequest['data']['email'])
            ->where(['status' => User::STATUS_ACTIVE])
            ->first();

        if (!$user || !Hash::check($validatedRequest['data']['password'], $user->password)) {
            return new JsonResponse(
                ['errors' => ['data.password' => ['fields.password.invalid']]],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if (Hash::needsRehash($user->password)) {
            Log::info("Rehashing password", ['user' => $user->id]);
            $user->password = Hash::make($validatedRequest['data']['password']);
            try {
                $user->saveOrFail();
            } catch (\Throwable $e) {
                /*
                 * It isn't a big deal if we get a DB error when saving the rehashed password because it can always
                 * pass the next time the user logs in.
                 */
                Log::warning("Unable to save password rehash", ['user' => $user->id]);
            }
        }

        $user->createToken(User::ACCESS_TOKEN_KEY);

        return new UserLoggedInResource($user);
    }

    /**
     * @param UserVerifyEmail $request
     * @return UserLoggedInResource
     * @throws \Throwable
     */
    public function verifyEmail(UserVerifyEmail $request): UserLoggedInResource
    {
        $validatedRequest = $request->validated();
        $user = User::whereEmailVerification($validatedRequest['data']['verification_code'])->first();

        \DB::transaction(function () use ($user) {
            $user->email_verification = null;
            $user->status = User::STATUS_ACTIVE;
            $user->save();
            $user->createToken(User::ACCESS_TOKEN_KEY);
        });

        return new UserLoggedInResource($user);
    }
}
