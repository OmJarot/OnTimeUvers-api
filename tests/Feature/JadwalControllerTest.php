<?php

namespace Tests\Feature;

use App\Models\Jadwal;
use App\Models\User;
use Database\Seeders\JadwalSeeder;
use Database\Seeders\JurusanSeeder;
use Database\Seeders\UserSearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class JadwalControllerTest extends TestCase
{
    public function testCreateSuccess(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->put("/api/jadwal/123", [
            "senin_1" => "android",
            "senin_2" => "android",
            "selasa_1" => "sp",
            "selasa_2" => "ks",
            "rabu_1" => "statistika",
            "rabu_2" => "statistika",
            "kamis_1" => "kp",
            "kamis_2" => "b.inggris 2",
            "jumat_1" => "edpl",
            "jumat_2" => "edpl"
        ], ["API-Key" => "dba"])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "id" => "123",
                    "senin_1" => "android",
                    "senin_2" => "android",
                    "selasa_1" => "sp",
                    "selasa_2" => "ks",
                    "rabu_1" => "statistika",
                    "rabu_2" => "statistika",
                    "kamis_1" => "kp",
                    "kamis_2" => "b.inggris 2",
                    "jumat_1" => "edpl",
                    "jumat_2" => "edpl"
                ]
            ]);

        $user = User::query()->where("id", "=", "123")->first();
        self::assertNotNull($user);

        $jadwal = $user->jadwal;
        self::assertNotNull($jadwal);
        self::assertEquals("android", $jadwal->senin_1);
        self::assertEquals("android", $jadwal->senin_2);
        self::assertEquals("sp", $jadwal->selasa_1);
        self::assertEquals("ks", $jadwal->selasa_2);
        self::assertEquals("edpl", $jadwal->jumat_2);
    }

    public function testCreateNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->put("/api/jadwal/1234", [
            "senin_1" => "android"
        ], ["API-Key" => "dba"])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Not Found"
                    ],
                ]
            ]);

//        $user = User::query()->where("id", "=", "123")->first();
//        self::assertNotNull($user);
//        $jadwal = $user->jadwal;
//        self::assertNull($jadwal);
    }

    public function testGetSuccess(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, JadwalSeeder::class]);

        $this->get("/api/jadwal/123", ["API-Key" => "dba"])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => "123",
                    "senin_1" => "android",
                    "senin_2" => "android",
                    "selasa_1" => "sp",
                    "selasa_2" => "ks",
                    "rabu_1" => "statistika",
                    "rabu_2" => "statistika",
                    "kamis_1" => "kp",
                    "kamis_2" => "b.inggris 2",
                    "jumat_1" => "edpl",
                    "jumat_2" => "edpl"
                ]
            ]);
    }

    public function testGetNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->get("/api/jadwal/1234", ["API-Key" => "dba"])
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Not Found"
                    ]
                ]
            ]);
    }

    public function testCreateSelectedSuccess(): void {
        $this->seed([JurusanSeeder::class, UserSearchSeeder::class]);

        $this->put("/api/jadwal",[
            "id" => ["1","2","3"],
            "jadwal" => [
                "senin_1" => "android",
                "senin_2" => "android",
            ]
        ], ["API-Key" => "dba"])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => ["1","2","3"],
                    "jadwal" => [
                        "senin_1" => "android",
                        "senin_2" => "android",
                    ]
                ]
            ]);

        $jadwal = Jadwal::query()->find("1");
        self::assertEquals("android", $jadwal->senin_1);
        self::assertEquals("android", $jadwal->senin_2);
        self::assertNull($jadwal->selasa_1);
        self::assertNull($jadwal->selasa_2);
        self::assertNull($jadwal->rabu_1);
        self::assertNull($jadwal->kamis_1);
        self::assertNull($jadwal->jumat_1);

        $jadwal = Jadwal::query()->find("2");
        self::assertEquals("android", $jadwal->senin_1);
        self::assertEquals("android", $jadwal->senin_2);
        self::assertNull($jadwal->selasa_1);
        self::assertNull($jadwal->selasa_2);
        self::assertNull($jadwal->rabu_1);
        self::assertNull($jadwal->kamis_1);
        self::assertNull($jadwal->jumat_1);
    }

    public function testCreateSelectedValidationError(): void {
        $this->seed([JurusanSeeder::class, UserSearchSeeder::class]);

        $this->put("/api/jadwal",[
            "id" => [],
            "jadwal" => [
                "senin_1" => "android",
                "senin_2" => "android",
            ]
        ], ["API-Key" => "dba"])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "id" => [
                        "The id field is required."
                    ]
                ]
            ]);
    }

    public function testCreateSelectedNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSearchSeeder::class]);

        $this->put("/api/jadwal",[
            "id" => ["1","2","80","90","321"],
            "jadwal" => [
                "senin_1" => "android",
                "senin_2" => "android",
            ]
        ], ["API-Key" => "dba"])
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Not Found" => ["80","90","321"]
                    ]
                ]
            ]);
        $jadwal = Jadwal::query()->find("1");
        self::assertNull($jadwal);
        $jadwal = Jadwal::query()->find("2");
        self::assertNull($jadwal);
        $jadwal = Jadwal::query()->find("80");
        self::assertNull($jadwal);
    }
}
