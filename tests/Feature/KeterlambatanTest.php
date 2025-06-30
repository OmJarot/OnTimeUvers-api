<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\JadwalSeeder;
use Database\Seeders\JurusanSeeder;
use Database\Seeders\KeterlambatanSeeder;
use Database\Seeders\SearchKeterlambatanSeed;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class KeterlambatanTest extends TestCase
{
    public function testInputSuccess(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, JadwalSeeder::class]);

        $this->post("/api/keterlambatan", [
            "waktu" => "30-06-2024 19:18"
        ], ["API-Key" => "test"])
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    "user_id" => "123",
                    "matkul" => "android",
                    "waktu" => "2024-06-30 19:18"
                ]
            ]);

        $user = User::query()->where("id", "=", "123")->first();
        self::assertNotNull($user);
        $keterlambatans = $user->keterlambatans()->count();
        self::assertEquals(1, $keterlambatans);
    }

    public function testInputDouble(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, JadwalSeeder::class, KeterlambatanSeeder::class]);

        $this->post("/api/keterlambatan", [
            "waktu" => "30-06-2024 19:18"
        ], ["API-Key" => "test"])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "cannot input double"
                    ]
                ]
            ]);
    }


    public function testInputValidationError(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, JadwalSeeder::class]);

        $this->post("/api/keterlambatan", [
            "waktu" => "30-06-2024 13:18"
        ], ["API-Key" => "test"])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "waktu" => [
                        "it's not too late"
                    ]
                ]
            ]);

        $user = User::query()->where("id", "=", "123")->first();
        self::assertNotNull($user);
        $keterlambatans = $user->keterlambatans()->count();
        self::assertEquals(0, $keterlambatans);
    }

    public function testWrongFormat(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, JadwalSeeder::class]);

        $this->post("/api/keterlambatan", [
            "waktu" => "salah"
        ], ["API-Key" => "test"])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "waktu" => [
                        "The waktu field must match the format d-m-Y H:i.",
                        "wrong format"
                    ]
                ]
            ]);

        $user = User::query()->where("id", "=", "123")->first();
        self::assertNotNull($user);
        $keterlambatans = $user->keterlambatans()->count();
        self::assertEquals(0, $keterlambatans);
    }


    public function testInputForbidden(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, JadwalSeeder::class]);

        $this->post("/api/keterlambatan", [
            "waktu" => "30-06-2024 13:18"
        ], ["API-Key" => "dba"])
            ->assertStatus(403);
    }

    public function testInputManualSuccess(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, JadwalSeeder::class]);

        $this->post("/api/keterlambatan/input", [
            "id" => "123",
            "name" => "piter",
            "waktu" => "30-06-2024 19:18"
        ], ["API-Key" => "security"])
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    "user_id" => "123",
                    "matkul" => "android",
                    "waktu" => "2024-06-30 19:18"
                ]
            ]);

        $user = User::query()->where("id", "=", "123")->first();
        self::assertNotNull($user);
        $keterlambatans = $user->keterlambatans()->count();
        self::assertEquals(1, $keterlambatans);
    }

    public function testInputManualDouble(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, JadwalSeeder::class, KeterlambatanSeeder::class]);

        $this->post("/api/keterlambatan/input", [
            "id" => "123",
            "name" => "piter",
            "waktu" => "30-06-2024 19:18"
        ], ["API-Key" => "security"])
            ->assertJson([
                "errors" => [
                    "message" => [
                        "cannot input double"
                    ]
                ]
            ]);
    }

    public function testInputManualValidationError(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, JadwalSeeder::class]);

        $this->post("/api/keterlambatan/input", [
            "id" => "",
            "name" => "",
            "waktu" => "das"
        ], ["API-Key" => "security"])
            ->assertStatus(400)->assertJson([
                "errors" => [
                    "id" => [
                        "The id field is required."
                    ],
                    "name" => [
                        "The name field is required."
                    ],
                    "waktu" => [
                        "The waktu field must match the format d-m-Y H:i.",
                        "wrong format"
                    ],
                ]
            ]);

    }

    public function testInputManualNotLate(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, JadwalSeeder::class]);

        $this->post("/api/keterlambatan/input", [
            "id" => "123",
            "name" => "piter",
            "waktu" => "30-06-2024 12:18"
        ], ["API-Key" => "security"])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "waktu" => [
                        "it's not too late"
                    ]
                ]
            ]);
        $user = User::query()->where("id", "=", "123")->first();
        self::assertNotNull($user);
        $keterlambatans = $user->keterlambatans()->count();
        self::assertEquals(0, $keterlambatans);
    }

    public function testInputManualForbidden(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, JadwalSeeder::class]);

        $this->post("/api/keterlambatan/input", [
            "id" => "123",
            "name" => "piter",
            "waktu" => "30-06-2024 12:18"
        ], ["API-Key" => "dba"])
            ->assertStatus(403);
    }

    public function testGet(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, KeterlambatanSeeder::class]);

        $this->get("/api/keterlambatan/123", headers: ["API-Key" => "test"])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    [
                        "user_id" => "123",
                        "matkul" => "android",
                        "waktu" => "2024-06-30 19:18:00"
                    ]
                ]
            ]);
    }

    public function testGetNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, KeterlambatanSeeder::class]);

        $this->get("/api/keterlambatan/12", headers: ["API-Key" => "test"])
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Not Found"
                    ]
                ]
            ]);
    }

    public function testSearchKeterlambatan(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, SearchKeterlambatanSeed::class]);

        $response = $this->get("/api/keterlambatan", ["API-Key" => "test"])
            ->assertStatus(200)
            ->json();

        self::assertEquals(10, count($response["data"]));
        self::assertEquals(10, $response["meta"]["total"]);
    }

    public function testSearchKeterlambatanByName(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, SearchKeterlambatanSeed::class]);

        $response = $this->get("/api/keterlambatan?name=piter", ["API-Key" => "test"])
            ->assertStatus(200)
            ->json();

        self::assertEquals(10, count($response["data"]));
        self::assertEquals(10, $response["meta"]["total"]);
    }

    public function testSearchKeterlambatanByJurusan(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, SearchKeterlambatanSeed::class]);

        $response = $this->get("/api/keterlambatan?jurusan=tpl", ["API-Key" => "test"])
            ->assertStatus(200)
            ->json();

        self::assertEquals(10, count($response["data"]));
        self::assertEquals(10, $response["meta"]["total"]);
    }

    public function testSearchKeterlambatanByAngkatan(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, SearchKeterlambatanSeed::class]);

        $response = $this->get("/api/keterlambatan?angkatan=2023", ["API-Key" => "test"])
            ->assertStatus(200)
            ->json();

        self::assertEquals(10, count($response["data"]));
        self::assertEquals(10, $response["meta"]["total"]);
    }

    public function testSearchKeterlambatanByDate(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, SearchKeterlambatanSeed::class]);

        $response = $this->get("/api/keterlambatan?date=2024-06-30", ["API-Key" => "test"])
            ->assertStatus(200)
            ->json();

        self::assertEquals(10, count($response["data"]));
        self::assertEquals(10, $response["meta"]["total"]);
    }

    public function testNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, SearchKeterlambatanSeed::class]);

        $response = $this->get("/api/keterlambatan?name=tidak ada", ["API-Key" => "test"])
            ->assertStatus(200)
            ->json();

        self::assertEquals(0, count($response["data"]));
        self::assertEquals(0, $response["meta"]["total"]);
    }

    public function testWithPage(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class, SearchKeterlambatanSeed::class]);

        $response = $this->get("/api/keterlambatan?size=5&page=2", ["API-Key" => "test"])
            ->assertStatus(200)
            ->json();

        self::assertEquals(5, count($response["data"]));
        self::assertEquals(2, $response["meta"]["current_page"]);
        self::assertEquals(10, $response["meta"]["total"]);
    }


}
