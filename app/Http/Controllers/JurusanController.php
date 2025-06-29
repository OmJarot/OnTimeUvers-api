<?php

namespace App\Http\Controllers;

use App\Http\Requests\CraeteJurusanRequest;
use App\Http\Resources\JurusanResource;
use App\Models\Jurusan;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class JurusanController extends Controller
{
    use AuthorizesRequests;

    public function create(CraeteJurusanRequest $request):JurusanResource {
        $user = Auth::user();
        $this->authorize("create", $user);

        $data = $request->validated();

        $jurusan = Jurusan::query()->make($data);
        $jurusan->id = $data["name"]." ".$data["angkatan"];

        if (Jurusan::query()->where("id", "=", $jurusan->id)->count() == 1){
            throw new HttpResponseException(response([
                "errors" => [
                    "id" => [
                        "jurusan already registered"
                    ]
                ]
            ],400));
        }

        $jurusan->save();

        return new JurusanResource($jurusan);
    }

    public function get(string $id): JurusanResource {
        $jurusan = Jurusan::query()->where("id", "=", $id)->first();

        if (!$jurusan){
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "Not Found"
                    ]
                ]
            ],404));
        }
        $this->authorize("view", $jurusan);

        return new JurusanResource($jurusan);
    }

    public function delete(string $id): JsonResponse {
        $jurusan = Jurusan::query()->where("id", "=", $id)->first();

        if (!$jurusan){
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "Not Found"
                    ]
                ]
            ],404));
        }
        $this->authorize("delete", $jurusan);
        $jurusan->delete();

        return response()->json(["data" => true])->setStatusCode(200);
    }

}
