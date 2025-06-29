<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\JurusanSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    public function testUser(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

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
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->post("/api/users/login", [
            "id" => "123",
            "password" => "piter"
        ])->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => "123",
                    "name" => "piter",
                    "jurusan" => "tpl",
                    "level" => "user"
                ]
            ]);

        $user = User::where("id", "123")->first();
        self::assertNotNull($user->id);
    }

    public function testLoginValidationError(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

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

    public function testCurrentSuccess(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->get("/api/users/current", headers:["API-Key" => "test"])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => "123",
                    "name" => "piter",
                    "jurusan" => "tpl",
                    "level" => "user"
                ]
            ]);
    }

    public function testCurrentInvalid(): void {
        $this->get("/api/users/current")
            ->assertStatus(401);
    }

    public function testUpdateSuccess(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->post("/api/users/update",
            data: [
                "oldPassword" => "piter",
                "newPassword" => "update",
                "retypePassword" => "update"
            ],
            headers: ["API-Key" => "test"])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => "123",
                    "name" => "piter",
                    "jurusan" => "tpl",
                    "level" => "user"
                ]
            ]);
    }

    public function testUpdateValidationError(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->post("/api/users/update",
            data: [
                "oldPassword" => "",
                "newPassword" => "",
                "retypePassword" => ""
            ],
            headers: ["API-Key" => "test"])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "oldPassword" => [
                        "The old password field is required."
                    ],
                    "newPassword" => [
                        "The new password field is required."
                    ],
                    "retypePassword" => [
                        "The retype password field is required."
                    ]
                ]
            ]);
    }

    public function testUpdateWrongOldPassword(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->post("/api/users/update",
            data: [
                "oldPassword" => "salah",
                "newPassword" => "test",
                "retypePassword" => "test"
            ],
            headers: ["API-Key" => "test"])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "old password is wrong"
                    ]
                ]
            ]);
    }

    public function testUpdateWrongRetypePassword(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->post("/api/users/update",
            data: [
                "oldPassword" => "piter",
                "newPassword" => "test",
                "retypePassword" => "salah"
            ],
            headers: ["API-Key" => "test"])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "retypePassword" => [
                        "The retype password field must match new password."
                    ]
                ]
            ]);
    }

    public function testLogoutSuccess(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->get("/api/users/logout", headers: ["API-Key" => "test"])
            ->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);
    }

    public function testCreateUserSuccess(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->post("/api/dba/create", [
            "id" => "122",
            "name" => "new",
            "password" => "new",
            "jurusan_id" => "tpl2023"
        ], ["API-Key" => "dba"])
            ->assertStatus(201)
            ->assertJson([
                "data" => [
                    "id" => "122",
                    "name" => "new",
                    "jurusan" => "tpl",
                    "level" => "user"
                ]
            ]);
    }

    public function testCreateUserForbidden(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->post("/api/dba/create", [
            "id" => "122",
            "name" => "new",
            "password" => "new",
            "jurusan_id" => "tpl2023"
        ], ["API-Key" => "test"])
            ->assertStatus(403);
    }

    public function testCreateUserValidationError(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->post("/api/dba/create", [
            "id" => "",
            "name" => "",
            "password" => "",
            "jurusan_id" => ""
        ], ["API-Key" => "dba"])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "id" => [
                        "The id field is required."
                    ],
                    "name" => [
                        "The name field is required."
                    ],
                    "password" => [
                        "The password field is required."
                    ],
                    "jurusan_id" =>[
                        "The jurusan id field is required."
                    ]
                ]
            ]);
    }

    public function testCreateUserAlreadyExist(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->post("/api/dba/create", [
            "id" => "123",
            "name" => "piter",
            "password" => "piter",
            "jurusan_id" => "tpl2023"
        ], ["API-Key" => "dba"])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "id" => [
                        "nim already registered"
                    ]
                ]
            ]);
    }


}
