<?php

namespace Tests\Feature;

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


}
