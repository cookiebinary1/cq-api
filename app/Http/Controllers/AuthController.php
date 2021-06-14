<?php

namespace App\Http\Controllers;

use App\Exceptions\EntityException;
use App\Exceptions\ErrorException;
use App\Models\Image;
use Exception;
use FusionAuth\ClientResponse;
use FusionAuth\FusionAuthClient;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\NoReturn;
use Laravel\Passport\Guards\TokenGuard;
use Laravel\Passport\Passport;
use League\OAuth2\Server\Exception\OAuthServerException;

/**
 * Class ApiAuthController
 * @package App\Http\Controllers\Auth
 * @author  Cookie
 */
class AuthController extends Controller
{
    private FusionAuthClient $authClient;

    /**
     * AuthController constructor.
     * @param FusionAuthClient $authClient
     */
    public function __construct(FusionAuthClient $authClient)
    {
        $this->authClient = $authClient;
    }

    /**
     * @param Request $request
     * @return Response|array|Application|ResponseFactory
     * @throws Exception
     */
    public function register(Request $request): Response|array|Application|ResponseFactory
    {
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users',
            'password'   => 'required|string|confirmed',
            'country_id' => 'integer',
        ]);

        if ($validator->fails()) {
            return error(2005, 422, implode("\n", $validator->errors()->all()));
        }

        // @todo set country of registered user
        $clientRequest = [
            'registration'         => ['applicationId' => config('fusionauth.app_id')],
            'sendSetPasswordEmail' => false,
            'skipVerification'     => false,
            'user'                 => [
                'username'               => request('name'),
                'password'               => request('password'),
                'email'                  => request('email'),
                'passwordChangeRequired' => false,
                'twoFactorEnabled'       => false,
            ],
        ];


        // @todo log every responses
        $clientResponse = $this->authClient->register(null, $clientRequest);

        \Log::info("Registration auth server response:", [$clientResponse]);

        if (!$clientResponse->wasSuccessful()) {
            return error(2005, 401, additionalData: [
                $clientResponse->errorResponse,
            ]);
        }

        $user = User::syncFusionUser($clientResponse->successResponse->user);

        if (!$user) {
            return error(2005, 401);
        }

        return ["status" => "ok", "user" => collect($user)->except("fusion_data")];

        //$token = $user->createToken('Laravel Password Grant Client')->accessToken;

        //return response(['token' => $token], 201);
    }

    /**
     * @param Request $request
     * @return \Application|\ResponseFactory|\Response
     * @throws Exception
     */
    public function login(Request $request)
    {
        switch ($type = request('type')) {
            case 'facebook':
            case 'google':
                // todo to config/env
                $identityProviderId = match ($type) {
                    'facebook' => '56abdcc7-8bd9-4321-9621-4e9bbebae494',
                    'google' => '82339786-3dff-42a6-aac6-1f1ceecb6c46',
                };

                $data = [
                    "applicationId"      => config('fusionauth.app_id'),
                    "data"               => [
                        "token" => request("token"),
                    ],
                    "identityProviderId" => $identityProviderId,
                    "ipAddress"          => real_ip_address(),
                ];

                // @todo log every responses
                $response = $this->authClient->identityProviderLogin($data);
                break;

            case 'basic':
            default:
                $validator = Validator::make($request->all(), [
                    'email'    => 'required|string|email|max:255',
                    'password' => 'required|string|min:6',
                ]);
                if ($validator->fails()) {
                    return error(2006, 422, implode("\n", $validator->errors()->all()));
                }

                $clientRequest = [
                    'applicationId' => config('fusionauth.app_id'),
                    "ipAddress"     => real_ip_address(),
                    'loginId'       => request('email'),
                    'password'      => request('password'),
                ];

                // @todo log every responses
                $response = $this->authClient->login($clientRequest);
                //dd($response);
                break;
        }

        return $this->loginResponseProcess($response);
    }

    /**
     * @return Application|ResponseFactory|Response
     * @throws Exception
     */
    public function twoFactorLogin()
    {
        return $this->loginResponseProcess($this->authClient->twoFactorLogin([
            'applicationId' => config('fusionauth.app_id'),
            'ipAddress'     => request()->ip(), // @todo real client ID / not the api server/api docker container
            'code'          => request('code'),
            'twoFactorId'   => request('twoFactorId'),
        ]));
    }

    /**
     * @param ClientResponse $response
     * @return Application|ResponseFactory|Response
     */
    private function loginResponseProcess(ClientResponse $response)
    {
        if ($response->wasSuccessful()) {
            $successResponse = $response->successResponse;
        } else {
            return error(2006, 401, additionalData: (array)$response);
        }

        if ($response->status == 242) {
            //@todo two factor,  status:242
            $twoFactorId = $successResponse->twoFactorId;

            return error(1001, 404, "todo: two factor id: $twoFactorId", (array)$successResponse);
        }

        $user = User::syncFusionUser($response->successResponse->user);

        if ($user) {

            if (@$user->fusion_data['verified'] != true) {
                return error(2007, 422);
            }

            $token = $user->createToken('Laravel Password Grant Client')->accessToken;

            return response(['token' => $token], 201);
        } else {
            return error(2004, 422);
        }
    }

    /**
     * @param Request $request
     * @return Application|ResponseFactory|Response
     */
    public function logout(Request $request)
    {
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];

        return response($response, 200);
    }

    /**
     * @throws Exception
     */
    public function verify()
    {
        $response = $this->authClient->verifyEmail(request('verification_id'));

        return match ($response->status) {
            200 => ['status' => 'ok'],
            default => error(2008, 404),
        };
    }
}
