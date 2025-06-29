<?php

namespace Tests\Feature;

use Database\Seeders\JurusanSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JadwalControllerTest extends TestCase
{
    public function testCreateSuccess(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->patch("/api/jadwal/123", [
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
    }

    public function testCreateNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->patch("/api/jadwal/1234", [
            "senin_1" => "android"
        ], ["API-Key" => "dba"])->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Not Found"
                    ],
                ]
            ]);
    }


}
