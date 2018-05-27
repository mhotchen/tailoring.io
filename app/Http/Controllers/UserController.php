<?php
namespace App\Http\Controllers;

use App\Http\Requests\UserAttemptLogin;
use App\Http\Requests\UserVerifyEmail;
use App\Http\Resources\UserLoggedInResource;
use App\Model\User;
use Auth;
use GuzzleHttp\Client as HttpClient;
use Hash;
use Illuminate\Http\JsonResponse;
use Log;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;

final class UserController extends Controller
{
    /**
     * @return UserLoggedInResource
     */
    public function getFromAuth(): UserLoggedInResource
    {
        return $this->getResponseResource(Auth::user());
    }

    /**
     * Don't rely on validators to verify the email/password because they will leak information about which field
     * is invalid. Do the hard work in the controller so we can use the same error type for all credential problems.
     * Annoyingly resources implement a Responsable interface whereas JsonResponse extends the Symfony Response class
     * so we can't do type hinting on the response of this method.
     *
     * @param UserAttemptLogin $request
     * @return UserLoggedInResource|JsonResponse
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function attemptLogin(UserAttemptLogin $userAttemptLogin)
    {
        $request = $userAttemptLogin->validated();

        /*
         * Make brute force impractical.
         */
        sleep(1);

        $oauthResponse = $this->getTokensResponse($request['data']['email'], $request['data']['password']);

        /*
         * The OAuth API doesn't handle status checks.
         */
        $user = User::whereEmail($request['data']['email'])
            ->where(['status' => User::STATUS_ACTIVE])
            ->first();

        if (!$user || $oauthResponse->getStatusCode() !== 200) {
            return new JsonResponse(
                ['errors' => ['data.password' => ['fields.password.invalid_email_or_password']]],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        /*
         * The built in OAuth stuff doesn't check if the password needs rehashed.
         */
        if (Hash::needsRehash($user->password)) {
            Log::info("Rehashing password", ['user' => $user->id]);
            $user->password = Hash::make($request['data']['password']);
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

        return $this->getResponseResource($user, $oauthResponse);
    }

    /**
     * @param UserVerifyEmail $userVerifyEmail
     * @return UserLoggedInResource|JsonResponse
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \Throwable
     */
    public function verifyEmail(UserVerifyEmail $userVerifyEmail)
    {
        $request = $userVerifyEmail->validated();

        /*
         * Make brute force impractical.
         */
        sleep(1);

        $errorResponse = new JsonResponse(
            ['errors' => ['data.password' => ['fields.password.invalid']]],
            Response::HTTP_UNPROCESSABLE_ENTITY
        );

        /*
         * The OAuth API doesn't handle status checks.
         */
        $user = User::whereEmailVerification($request['data']['verification_code'])
            ->where(['status' => User::STATUS_AWAITING_EMAIL_VERIFICATION])
            ->first();

        if (!$user) {
            return $errorResponse;

        }

        $oauthResponse = $this->getTokensResponse($user->email, $request['data']['password']);
        if ($oauthResponse->getStatusCode() !== 200) {
            return $errorResponse;
        }

        \DB::transaction(function () use ($user) {
            $user->email_verification = null;
            $user->status = User::STATUS_ACTIVE;
            $user->save();
        });

        return $this->getResponseResource($user, $oauthResponse);
    }

    /**
     * Personal access tokens AREN'T a valid JWT token so they don't work with the auth:api middleware. This means
     * we must use the password grant type, which means we need a client secret, which should never be visible to
     * the frontend application.
     *
     * The Passport OAuth stuff is pretty verbose with its various encryption keys and such so the easiest method
     * is to use the API provided.
     *
     * TODO: Lock down /oauth routes to only allow internal requests
     *
     * @param string $email
     * @param string $password
     * @return \GuzzleHttp\Psr7\Response
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    private function getTokensResponse(string $email, string $password): ResponseInterface
    {
        return (new HttpClient(['http_errors' => false]))->post(
            sprintf('%s/oauth/token', env('APP_URL')),
            [
                'form_params' => [
                    'grant_type' => 'password',
                    'client_id' => env('OAUTH_PASSWORD_CLIENT_ID'),
                    'client_secret' => env('OAUTH_PASSWORD_CLIENT_SECRET'),
                    'username' => $email,
                    'password' => $password,
                    'scope' => '',
                ],
            ]
        );
    }

    /**
     * @param User                   $user
     * @param ResponseInterface|null $oauthResponse
     * @return UserLoggedInResource
     */
    private function getResponseResource(User $user, ResponseInterface $oauthResponse = null): UserLoggedInResource
    {
        $resource = new UserLoggedInResource($user);
        if ($oauthResponse) {
            $resource->additional([
                'meta' => [
                    'jwt' => json_decode((string) $oauthResponse->getBody())
                ]
            ]);
        }

        return $resource;
    }
}
