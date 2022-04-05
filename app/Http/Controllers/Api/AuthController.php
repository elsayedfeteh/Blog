<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Http\Traits\ResponseJsonTrait;
use App\Models\User;
use App\Notifications\UserRegisterionNotification;
use App\Notifications\WelcomeMessageNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

class AuthController extends Controller
{
    use ResponseJsonTrait;

   /**
    * Create a new AuthController instance.
    *
    * @return void
    */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Register User
     *
     * @group Authentication
     *
     * @bodyParam name string required, email email required, password required
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            "name" => $request->post('name'),
            "email" => $request->post('email'),
            "password" => Hash::make($request->post('password')),
        ]);

        $user->Profile()->create();

        Notification::send(User::where('type', 'admin')->get(), new UserRegisterionNotification($user));
        $user->notify(new WelcomeMessageNotifications($user));

        // $token = auth('api')->login($user);

        // return $this->respondWithToken($token);

        return new UserResource($user);
    }

    /**
     * Login User
     *
     * @group Authentication
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (! $token = auth('api')->attempt($credentials))
        {
            return $this->responseJson(false, 'Unauthorized', null,401);
            // return response()->json([
            //     'status' => false,
            //     'message' => 'Unauthorized',
            //     'data' => null,
            // ], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Logout User (Invalidate the token)
     *
     * @group Authentication
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    /**
     * Get the token array structure
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
