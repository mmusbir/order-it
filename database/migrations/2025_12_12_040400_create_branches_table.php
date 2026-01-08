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
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('branch_code')->unique(); // ID Cabang
            $table->string('name'); // Nama Cabang
            $table->string('pic_name')->nullable(); // PIC Name
            $table->string('phone')->nullable(); // Phone
            $table->text('address')->nullable(); // Alamat Cabang
            $table->string('google_maps_url')->nullable(); // Google Maps URL
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
