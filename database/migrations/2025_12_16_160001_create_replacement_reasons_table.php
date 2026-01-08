<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('replacement_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default data
        \DB::table('replacement_reasons')->insert([
            ['name' => 'Aging (Peremajaan)', 'slug' => 'AGING', 'description' => 'Aset sudah melewati masa pakai', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Broken (Rusak)', 'slug' => 'BROKEN', 'description' => 'Aset rusak dan tidak bisa diperbaiki', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lost (Hilang)', 'slug' => 'LOST', 'description' => 'Aset hilang', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('replacement_reasons');
    }
};
