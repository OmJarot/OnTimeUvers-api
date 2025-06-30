<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\JurusanSeeder;
use Database\Seeders\UserSearchSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use function PHPUnit\Framework\assertNotNull;

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

    public function testActing(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        //auth biasa
        $user = User::query()->where("id", "=", "123")->first();
        $this->actingAs($user)->get("/");
        self::assertTrue(true);
    }

    public function testLogins(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $user = User::query()->where("id", "=", "123")->first();
        Auth::login($user);
        //langsung login
        $user = Auth::user();
        self::assertNotNull($user);
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
        self::assertNotNull($user->token);
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

        $this->patch("/api/users/update",
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

        $user = User::query()->where("token", "=", "test")->first();
        self::assertTrue(Hash::check("update", $user->password));
    }

    public function testUpdateValidationError(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->patch("/api/users/update",
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

        $this->patch("/api/users/update",
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

        $this->patch("/api/users/update",
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

        $this->delete("/api/users/logout", headers: ["API-Key" => "test"])
            ->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);

        $user = User::where("id", "123")->first();
        self::assertNull($user->token);
    }

    public function testCreateUserSuccess(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->post("/api/dba/users", [
            "id" => "122",
            "name" => "new",
            "password" => "new",
            "jurusan_id" => "tpl 2023"
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

        $user = User::query()->where("id", "=", "122")->first();
        self::assertNotNull($user);
        self::assertEquals("new", $user->name);
    }

    public function testCreateUserForbidden(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->post("/api/dba/users", [
            "id" => "122",
            "name" => "new",
            "password" => "new",
            "jurusan_id" => "tpl2023"
        ], ["API-Key" => "test"])
            ->assertStatus(403);
    }

    public function testCreateUserValidationError(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->post("/api/dba/users", [
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

        $this->post("/api/dba/users", [
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

    public function testDelete(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $user = User::query()->where("id", "=", "123")->first();
        $user->jadwal()->create([
            "id" => $user->id
        ]);

        $this->delete("/api/dba/users/".$user->id, headers: ["API-Key" => "dba"])
            ->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);

        $user = User::query()->where("id", "=", "123")->first();
        self::assertNull($user);
    }

    public function testDeleteNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->delete("/api/dba/users/salah", headers: ["API-Key" => "dba"])
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Not Found"
                    ]
                ]
            ]);
    }

    public function testDeleteForbidden(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $user = User::query()->where("id", "=", "123")->first();

        $this->delete("/api/dba/users/".$user->id, headers: ["API-Key" => "test"])
            ->assertStatus(403);
    }

    public function testUpdateUser(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->put("/api/dba/users", [
            "id" => "123",
            "name" => "new",
            "password" => "new",
            "jurusan" => "tpl2023"
        ], ["API-Key" => "dba"])
            ->assertStatus(200)
            ->assertJson([
                "data" => [
                    "id" => "123",
                    "name" => "new",
                    "jurusan" => "tpl",
                    "level" => "user"
                ]
            ]);

        $user = User::query()->where("id", "=", "123")->first();
        self::assertNotNull($user);
        self::assertEquals("new", $user->name);
    }

    public function testUpdateUserValidationError(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->put("/api/dba/users", [
            "id" => "123",
            "name" => "",
            "password" => "",
            "jurusan" => ""
        ], ["API-Key" => "dba"])
            ->assertStatus(400)
            ->assertJson([
                "errors" => [
                    "name" => [
                        "The name field is required."
                    ],
                    "password" => [
                        "The password field is required."
                    ],
                    "jurusan" =>[
                        "The jurusan field is required."
                    ]
                ]
            ]);
    }

    public function testUpdateUserNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->put("/api/dba/users", [
            "id" => "12334",
            "name" => "new",
            "password" => "new",
            "jurusan" => "tpl2023"
        ], ["API-Key" => "dba"])
            ->assertStatus(404)
            ->assertJson([
                "errors" => [
                    "message" => [
                        "Not Found"
                    ]
                ]
            ]);
    }

    public function testUpdateUserForbidden(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->put("/api/dba/users", [
            "id" => "12334",
            "name" => "new",
            "password" => "new",
            "jurusan" => "tpl2023"
        ], ["API-Key" => "test"])
            ->assertStatus(403);
    }

    public function testSearch(): void {
        $this->seed([JurusanSeeder::class, UserSearchSeeder::class]);

        $response = $this->get("/api/users", headers: ["API-Key" => "dba"])
            ->assertStatus(200)
            ->json();

        self::assertEquals(10, count($response["data"]));
        self::assertEquals(21, $response["meta"]["total"]);

    }

    public function testSearchName(): void {
        $this->seed([JurusanSeeder::class, UserSearchSeeder::class]);

        $response = $this->get("/api/users?name=user", headers: ["API-Key" => "dba"])
            ->assertStatus(200)
            ->json();

        self::assertEquals(10, count($response["data"]));
        self::assertEquals(20, $response["meta"]["total"]);
    }

    public function testSearchJurusan(): void {
        $this->seed([JurusanSeeder::class, UserSearchSeeder::class]);

        $response = $this->get("/api/users?jurusan=tpl", headers: ["API-Key" => "dba"])
            ->assertStatus(200)
            ->json();

        self::assertEquals(10, count($response["data"]));
        self::assertEquals(20, $response["meta"]["total"]);
    }

    public function testSearchAngkatan(): void {
        $this->seed([JurusanSeeder::class, UserSearchSeeder::class]);

        $response = $this->get("/api/users?angkatan=2023", headers: ["API-Key" => "dba"])
            ->assertStatus(200)
            ->json();

        self::assertEquals(10, count($response["data"]));
        self::assertEquals(21, $response["meta"]["total"]);
    }

    public function testSearchAll(): void {
        $this->seed([JurusanSeeder::class, UserSearchSeeder::class]);

        $response = $this->get("/api/users?name=user&jurusan=tpl&angkatan=2023", headers: ["API-Key" => "dba"])
            ->assertStatus(200)
            ->json();

        self::assertEquals(10, count($response["data"]));
        self::assertEquals(20, $response["meta"]["total"]);
    }

    public function testSearchWithPage(): void {
        $this->seed([JurusanSeeder::class, UserSearchSeeder::class]);

        $response = $this->get("/api/users?size=5&page=2", headers: ["API-Key" => "dba"])
            ->assertStatus(200)
            ->json();

        self::assertEquals(5, count($response["data"]));
        self::assertEquals(2, $response["meta"]["current_page"]);
        self::assertEquals(21, $response["meta"]["total"]);
    }

    public function testNotFound(): void {
        $this->seed([JurusanSeeder::class, UserSearchSeeder::class]);

        $response = $this->get("/api/users?name=tidak", headers: ["API-Key" => "dba"])
            ->assertStatus(200)
            ->json();

        self::assertEquals(0, count($response["data"]));
        self::assertEquals(0, $response["meta"]["total"]);
    }

    public function testSearchForbidden(): void {
        $this->seed([JurusanSeeder::class, UserSeeder::class]);

        $this->get("/api/users?name=user", headers: ["API-Key" => "test"])
            ->assertStatus(403);
    }



}
