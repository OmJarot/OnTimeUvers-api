<?php

namespace Database\Seeders;

use App\Models\Jadwal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Jadwal::query()->create([
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
        ]);
    }
}
