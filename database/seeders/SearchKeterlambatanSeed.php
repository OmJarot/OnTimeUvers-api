<?php

namespace Database\Seeders;

use App\Models\Keterlambatan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SearchKeterlambatanSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        for ($i = 0; $i < 10; $i++) {
            Keterlambatan::create([
                "user_id" => "123",
                "matkul" => "android",
                "waktu" => "2024-06-30 19:18"
            ]);
        }
    }
}
