<?php

namespace App\Http\Controllers;

use App\Http\Resources\RoomResource;
use App\Http\Resources\UserResource;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api')->except(['login']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return AnonymousResourceCollection
     */
    public function index()
    {
        return UserResource::collection(User::all());
    }

    public function login(Request $request)
    {
        Config::set('jwt.ttl', 60*60*7);

        $credentials = $request->only(['user_name', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            $user = User::create([
                'user_name' => $request->user_name,
                'password' => bcrypt($request->password),
            ]);

            $token = auth()->login($user);
        }

        return $this->respondWithToken(auth()->getUser(), $token);
    }

    /**
     * Display the specified resource.
     *
     * @param User $user
     * @return UserResource
     */
    public function show(User $user)
    {
        return new UserResource($user);
    }

    protected function respondWithToken(User $user, $token)
    {
        return response()->json([
            'user_id' => $user->id,
            'user_name' => $user->userName,
            'credentials' => $token,
        ]);
    }
}
