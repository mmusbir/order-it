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
        Schema::create('request_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default data
        \DB::table('request_types')->insert([
            ['name' => 'New Hire / New Branch', 'slug' => 'NEW_HIRE', 'description' => 'Request untuk karyawan baru atau cabang baru', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Replacement', 'slug' => 'REPLACEMENT', 'description' => 'Request untuk penggantian aset lama', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_types');
    }
};
