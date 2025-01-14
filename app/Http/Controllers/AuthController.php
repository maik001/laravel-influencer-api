<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateInfoRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController
{
    public function login(Request $request) {
        if(Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $scope = $request->input('scope');

            if($user->is_influencer() && $scope !== 'influencer') {
                return response([
                    'error' => 'Access denied',
                ], Response::HTTP_FORBIDDEN);
            }

            $token = $user->createToken($scope, [$scope])->accessToken;

            $cookie = Cookie('jwt', $token, 3600);

            return response([
                'token' => $token,
            ])->withCookie($cookie);
        }

        return response([
            'error' => 'Invalid credentials',
        ], Response::HTTP_UNAUTHORIZED);
    }

    public function logout() {
        $cookie = Cookie::forget('jwt');

        return response([
            'message' => 'success'
        ])->withCookie($cookie);
    }

    public function register(RegisterRequest $request) {
        $user = User::create($request->only('first_name', 'last_name', 'email') 
        + [
            'password' => Hash::make($request->input('password')),
            'role_id' => 1,
            'is_influencer' => 1
        ]);

        return response($user, Response::HTTP_CREATED);
    }

    public function user() {
        $user = Auth::user();
        
        $resource = new UserResource($user);

        if($user->is_influencer()) {
            return $resource;
        }

        return $resource->additional([
            'data' => [
                'permissions' => $user->permissions()
            ]
        ]);
    }

    public function update_info(UpdateInfoRequest $request) {
        $user = Auth::user();

        $user->update($request->only('first_name', 'last_name', 'email'));

        return response(new UserResource($user), Response::HTTP_ACCEPTED);
    }

    public function update_password(UpdatePasswordRequest $request) {
        $user = Auth::user();

        $user->update([
            'password' => Hash::make($request->input('password'))
        ]);

        return response(new UserResource($user), Response::HTTP_ACCEPTED);
    }
}
