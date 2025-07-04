<?php

namespace App\Http\Controllers;

use App\Http\Requests\InputKeterlambatanRequest;
use App\Http\Requests\InputManualRequest;
use App\Http\Resources\KeterlambatanCollection;
use App\Http\Resources\KeterlambatanResource;
use App\Http\Resources\SearchKeterlambatanCollection;
use App\Http\Resources\UserCollection;
use App\Models\Keterlambatan;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class KeterlambatanController extends Controller
{
    public function input(InputKeterlambatanRequest $request): KeterlambatanResource {
        $this->authorize("create", Keterlambatan::class);
        $data = $request->validated();
        $user = Auth::user();

        $date = Carbon::parse($data["waktu"])->toDateString();
        if ($user->keterlambatans()->whereDate("waktu", $date)->exists()){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "cannot input double"
                    ]
                ]
            ])->setStatusCode(400));
        }

        $sesi = $this->getSesi($data["waktu"]);

        if (!$sesi){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "it's not too late "
                    ]
                ]
            ])->setStatusCode(400));
        }
        $matkul = optional($user->jadwal)->$sesi;

        if (is_null($matkul)) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Mata kuliah tidak ditemukan pada sesi {$sesi}"
                    ]
                ]
            ], 404));
        }

        $keterlambatan = Keterlambatan::create([
            "user_id" => $user->id,
            "matkul" => $matkul,
            "waktu" => Carbon::parse($data["waktu"])->format("Y-m-d H:i")
        ]);
        return new KeterlambatanResource($keterlambatan);
    }

    public function inputById(InputManualRequest $request): KeterlambatanResource {
        $this->authorize("create", Keterlambatan::class);

        $data = $request->validated();
        $sesi = $this->getSesi($data["waktu"]);
        if (!$sesi){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "it's not too late "
                    ]
                ]
            ])->setStatusCode(400));
        }
        $user = User::query()->where("id", "=", $data["id"])->first();
        if (!$user){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "User not found : ". $data["id"]
                    ]
                ]
            ], 404));
        }
        $date = Carbon::parse($data["waktu"])->toDateString();
        if ($user->keterlambatans()->whereDate("waktu", $date)->exists()){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "cannot input double"
                    ]
                ]
            ])->setStatusCode(400));
        }
        $matkul = optional($user->jadwal)->$sesi;

        if (is_null($matkul)) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Mata kuliah tidak ditemukan pada sesi {$sesi}"
                    ]
                ]
            ], 404));
        }

        $keterlambatan = Keterlambatan::create([
            "user_id" => $user->id,
            "matkul" => $matkul,
            "waktu" => Carbon::parse($data["waktu"])->format("Y-m-d H:i")
        ]);
        return new KeterlambatanResource($keterlambatan);
    }

    public function get(string $id): KeterlambatanCollection {
        $this->authorize("viewAny", Keterlambatan::class);

        $keterlambatans = Keterlambatan::query()->where("user_id", "=", $id)->get();
        if ($keterlambatans->count() < 1){
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => [
                        "Not Found"
                    ]
                ]
            ], 404));
        }
        return new KeterlambatanCollection($keterlambatans);
    }

    public function search(Request $request): SearchKeterlambatanCollection {
        $this->authorize("viewAny", Keterlambatan::class);

        $page = $request->input("page", 1);
        $size = $request->input("size", 10);

        $name = $request->query("name");
        $jurusan = $request->query("jurusan");
        $angkatan = $request->query("angkatan");
        $tanggal = $request->query("tanggal");

        $query = Keterlambatan::query()
            ->with("user")
            ->whereHas("user", function ($query) use ($name, $jurusan, $angkatan) {
                if ($name) {
                    $query->where("name", "like", "%{$name}%");
                }
                if ($jurusan) {
                    $query->where("jurusan_id", "like", "%{$jurusan}%");
                }
                if ($angkatan) {
                    $query->where("jurusan_id", "like", "%{$angkatan}%");
                }
            });

        if ($tanggal){
            $query->whereDate("waktu", $tanggal);
        }

        $keterlambatan = $query->paginate(perPage: $size, page: $page);
        return new SearchKeterlambatanCollection($keterlambatan);

    }

    private function getSesi(string $waktu) {
        $key = collect([
            0 => [
                "senin_1",
                "senin_2",
            ],
            1 => [
                "selasa_1",
                "selasa_2",
            ],
            2 => [
                "rabu_1",
                "rabu_2",
            ],
            3 => [
                "kamis_1",
                "kamis_2",
            ],
            4 => [
                "jumat_1",
                "jumat_2"
            ],
        ]);
        try {
            $date = Carbon::parse($waktu);
        }catch (\Exception){
            Log::info("Invalid format {$waktu}");
            return null;
        }

        $day = $date->dayOfWeek;

        if ($day >= 5){
            return null;
        }

        $time = $date->format("H:i");

        if ($time >= "18:30" && $time <= "20:30"){
            return $key->get($day)[0];
        }elseif ($time >= "20:30" && $time <= "22:00"){
            return $key->get($day)[1];
        }else{
            return null;
        }
    }
}
