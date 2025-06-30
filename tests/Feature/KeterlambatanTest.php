<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\JadwalSeeder;
use Database\Seeders\JurusanSeeder;
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


}
