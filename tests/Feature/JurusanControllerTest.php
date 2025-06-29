<?php

namespace Tests\Feature;

use App\Models\Jurusan;
use Database\Seeders\JurusanSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JurusanControllerTest extends TestCase
{
    public function testCreateSuccess(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->post("/api/jurusans", [
            "name" => "tpl",
            "angkatan" => 2023
        ], [
            "API-Key" => "dba"
        ])->assertStatus(201)
            ->assertJson([
                "data" => [
                    "id" => "tpl 2023",
                    "name" => "tpl",
                    "angkatan" => "2023"
                ]
            ]);
    }

    public function testCreateValidationError(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->post("/api/jurusans", [
            "name" => "",
            "angkatan" => 4023
        ], [
            "API-Key" => "dba"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => ["The name field is required."],
                    "angkatan" => ["The angkatan field must be less than 2100."]
                ]
            ]);
    }

    public function testCreateAlreadyExist(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->post("/api/jurusans", [
            "name" => "tpl",
            "angkatan" => 2023
        ], [
            "API-Key" => "dba"
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "id" => [
                        "jurusan already registered"
                    ]
                ]
            ]);
    }

    public function testCreateForbidden(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->post("/api/jurusans", [
            "name" => "tpl",
            "angkatan" => 2023
        ], [
            "API-Key" => "test"
        ])->assertStatus(403);
    }


    public function testGetJurusan(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->get("/api/jurusans/tpl 2023", ["API-Key" => "dba"])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => "tpl 2023",
                    "name" => "tpl",
                    "angkatan" => "2023"
                ]
            ]);
    }

    public function testGetJurusanNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->get("/api/jurusans/tpl 2024", ["API-Key" => "dba"])
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Not Found"
                    ]
                ]
            ]);
    }

    public function testGetForbidden(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->get("/api/jurusans/tpl 2023", ["API-Key" => "test"])
            ->assertStatus(403);
    }

    public function testDeleteSuccess(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        Jurusan::create([
            "id" => "tpl 2024",
            "name" => "tpl",
            "angkatan" => 2023
        ]);

        $this->delete("/api/jurusans/tpl 2024", headers: ["API-Key" => "dba"])
            ->assertStatus(200)
            ->assertJson([
                "data" => "ok"
            ]);

        $jurusan = Jurusan::query()->where("id", "=", "tpl 2024")->first();
        self::assertNull($jurusan);
    }

    public function testDeleteNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->delete("/api/jurusans/tpl 2024", headers: ["API-Key" => "dba"])
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Not Found"
                    ]
                ]
            ]);

    }

    public function testForbidden(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->delete("/api/jurusans/tpl 2023", headers: ["API-Key" => "test"])
            ->assertStatus(403);

    }


}
