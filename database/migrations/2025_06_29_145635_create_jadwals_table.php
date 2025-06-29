<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jadwals', function (Blueprint $table) {
            $table->string("id", 20)->nullable(false)->primary();
            $table->string("senin_1", 100)->nullable();
            $table->string("senin_2", 100)->nullable();
            $table->string("selasa_1", 100)->nullable();
            $table->string("selasa_2", 100)->nullable();
            $table->string("rabu_1", 100)->nullable();
            $table->string("rabu_2", 100)->nullable();
            $table->string("kamis_1", 100)->nullable();
            $table->string("kamis_2", 100)->nullable();
            $table->string("jumat_1", 100)->nullable();
            $table->string("jumat_2", 100)->nullable();
            $table->foreign("id")->references("id")->on("users");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};
