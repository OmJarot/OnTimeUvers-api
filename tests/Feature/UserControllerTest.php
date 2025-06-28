<?php

namespace Tests\Feature;

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
        ])->assertStatus(200);

    }


}
