<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Jurusan::create([
            "id" => "tpl2023",
            "name" => "tpl",
            "angkatan" => 2023
        ]);

        Jurusan::create([
            "id" => "dba",
            "name" => "dba",
            "angkatan" => 2023
        ]);
    }
}
