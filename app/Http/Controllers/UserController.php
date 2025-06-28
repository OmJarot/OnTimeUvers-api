<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

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
            return new UserResource(Auth::user());
        }else{
            throw new \HttpResponseException(response([
                "error" =>[
                    "message" => [
                        "username or password is wrong"
                    ]
                ]
            ], 401));
        }
    }
}
