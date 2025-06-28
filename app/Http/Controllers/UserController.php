<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function login(LoginRequest $request): UserResource {
        $data = $request->validated();

        $login = Auth::attempt([
            "id" => $data["id"],
            "password" => $data["password"]
        ]);

        if ($login){
            Session::regenerate();

            $user = Auth::user();
            $user->token = (string) Str::uuid();
            $user->save();

            return new UserResource($user);
        }else{
            throw new HttpResponseException(response([
                "errors" =>[
                    "message" => [
                        "username or password is wrong"
                    ]
                ]
            ], 401));
        }
    }
}
