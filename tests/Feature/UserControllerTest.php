<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    public function testUser(): void {
        $this->seed([UserSeeder::class]);

        $success = Auth::attempt([
            "id" => "123",
            "password" => "piter"
        ]);

        self::assertTrue($success);

        $user = Auth::user();
        self::assertNotNull($user);
        self::assertEquals("123", $user->id);
    }

    public function testLogin(): void {
        $this->seed([UserSeeder::class]);

        $this->post("/api/users/login", [
            "id" => "123",
            "password" => "piter"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => "123",
                    "name" => "piter",
                    "level" => "user"
                ]
            ]);

        $user = User::where("id", "123")->first();
        self::assertNotNull($user->id);
    }

    public function testLoginValidationError(): void {
        $this->seed([UserSeeder::class]);

        $this->post("/api/users/login", [
            "id" => "",
            "password" => ""
        ])->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "id" => [
                        "The id field is required."
                    ],
                    "password" => [
                        "The password field is required."
                    ]
                ]
            ]);
    }

    public function testLoginWrongPassword(): void {
        $this->post("/api/users/login", [
            "id" => "salah",
            "password" => "salah"
        ])->assertStatus(401)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "username or password is wrong"
                    ]
                ]
            ]);
    }


}
