<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
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

    public function updatePassword(UpdatePasswordRequest $request): UserResource {
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

    public function logout(): JsonResponse {
        $user = Auth::user();
        $this->authorize("update", $user);
        $user->token = null;
        $user->save();
        Session::invalidate();
        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }

    public function createUser(CreateUserRequest $request): UserResource {
        $user = Auth::user();
        $this->authorize("create", $user);
        $data = $request->validated();

        if (User::query()->where("id", "=", $data["id"])->count() == 1){
            throw new HttpResponseException(response([
                "errors" => [
                    "id" => [
                        "nim already registered"
                    ]
                ]
            ],400));
        }

        $newUser = User::make($data);
        $newUser->level = "user";
        $newUser->password = Hash::make($newUser->password);
        $newUser->save();
        return new UserResource($newUser);
    }

    public function delete(string $id): JsonResponse {
        $user = Auth::user();
        $this->authorize("delete", $user);

        $db = User::query()->where("id", "=", $id)->first();
        if (!isset($db)){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Not found"
                    ]
                ]
            ])->setStatusCode(404));
        }
        $db->delete();
        return response()->json([
            "data" => true
        ])->setStatusCode(200);
    }

    public function updateUser(UpdateUserRequest $request): UserResource {
        $data = $request->validated();
        $user = User::query()->where("id", "=", $data["id"])->first();
        if (!$user){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Not found"
                    ]
                ]
            ])->setStatusCode(404));
        }
        $this->authorize("update", $user);

        $user->fill($data);
        $user->password = Hash::make($data["password"]);
        $user->save();
        return new UserResource($user);
    }

    public function search(Request $request) {
        $user = Auth::user();
        $this->authorize("viewAny", $user);

        $page = $request->input("page", 1);
        $size = $request->input("size", 10);

        $query = User::query();

        if ($name = $request->query("name")) {
            $query->where("name", "like", "%$name%");
        }

        $jurusan = $request->query("jurusan");
        $angkatan = $request->query("angkatan");

        if ($jurusan || $angkatan) {
            $query->whereHas("jurusan", function ($bJurusan) use ($jurusan, $angkatan) {
                if ($jurusan) {
                    $bJurusan->where("jurusans.name", "=", $jurusan);
                }
                if ($angkatan) {
                    $bJurusan->where("jurusans.angkatan", "=", $angkatan);
                }
            });
        }

        $users = $query->paginate(perPage: $size, page: $page);

        return new UserCollection($users);
    }
}
