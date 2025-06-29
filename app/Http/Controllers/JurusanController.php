<?php

namespace App\Http\Controllers;

use App\Http\Requests\CraeteJurusanRequest;
use App\Http\Resources\JurusanResource;
use App\Models\Jurusan;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Exceptions\HttpResponseException;
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

    public function get(string $id) {

    }
}
