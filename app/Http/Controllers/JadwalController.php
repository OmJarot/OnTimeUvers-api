<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateJadwalRequest;
use App\Http\Resources\JadwalResource;
use App\Models\Jadwal;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    use AuthorizesRequests;

    public function create(string $id, CreateJadwalRequest $request): JadwalResource {
        $this->authorize("create", Jadwal::class);
        $data = $request->validated();

        $user = User::query()->where("id", "=", $id)->first();
        if (!isset($user)){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Not Found"
                    ]
                ]
            ])->setStatusCode(404));
        }

        $jadwal = Jadwal::query()->updateOrCreate(["id" => $id], $data);

        return new JadwalResource($jadwal);
    }

    public function get(string $id): JadwalResource {
        $jadwal = Jadwal::query()->where("id", "=", $id)->first();
        if (!$jadwal){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Not Found"
                    ]
                ]
            ])->setStatusCode(404));
        }
        $this->authorize("view", $jadwal);
        return new JadwalResource($jadwal);
    }
}
