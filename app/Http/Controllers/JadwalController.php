<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateJadwalRequest;
use App\Http\Requests\CreateSelectedJadwalRequest;
use App\Http\Resources\JadwalCollection;
use App\Http\Resources\JadwalResource;
use App\Models\Jadwal;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
{
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

    public function createSelected(CreateSelectedJadwalRequest $request): JsonResponse {
        $data = $request->validated();
        $this->authorize("create", Jadwal::class);

        $ids = collect($data["id"]);
        $jadwal = collect($data["jadwal"]);

        DB::transaction(function () use ($ids, $jadwal){
            $notFound = collect([]);

            $ids->each(function ($value) use ($jadwal, &$notFound){
                $user = User::query()->where("id", "=", $value)->first();
                if (!$user){
                    $notFound->push($value);
                    return true;
                }
                $user->jadwal()->updateOrCreate(
                    ['id' => $user->id],
                    $jadwal->toArray()
                );
            });

            if ($notFound->isNotEmpty()){
                throw new HttpResponseException(response()->json([
                    "errors" => [
                        "message" => [
                            "Not Found" => $notFound->toArray()
                        ]
                    ]
                ])->setStatusCode(404));
            }

        });

        return response()->json([
            "data" => $data
        ])->setStatusCode(200);
    }
}
