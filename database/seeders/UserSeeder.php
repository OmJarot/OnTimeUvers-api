<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->create([
            "id" => "123",
            "name" => "piter",
            "password" => Hash::make("piter"),
            "level" => "user",
            "jurusan_id" => "tpl2023",
            "token" => "test"
        ]);

        User::query()->create([
            "id" => "dba",
            "name" => "dba",
            "password" => Hash::make("dba"),
            "level" => "dba",
            "jurusan_id" => "dba",
            "token" => "dba"
        ]);

    }
}
