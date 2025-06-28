<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class UserController extends Controller
{
    use AuthorizesRequests;

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

    public function current(): UserResource {
        $user = Auth::user();
        $this->authorize("view", $user);
        return new UserResource($user);
    }

    public function updateCurrent(UpdateUserRequest $request): UserResource {
        $data = $request->validated();
        $user = Auth::user();
        $this->authorize("update", $user);

        if (Hash::check($data["oldPassword"],$user->password)){
            $user->password = Hash::make($data["newPassword"]);
            $user->save();

            return new UserResource($user);
        }else{
            throw new HttpResponseException(response([
                "errors" =>[
                    "message" => [
                        "old password is wrong"
                    ]
                ]
            ], 400));
        }
    }
}
