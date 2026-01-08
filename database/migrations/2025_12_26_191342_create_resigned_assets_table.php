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
        Schema::create('resigned_assets', function (Blueprint $table) {
            $table->id();
            $table->string('snipeit_asset_id')->unique();
            $table->string('asset_tag');
            $table->string('asset_name');
            $table->string('serial_number')->nullable();
            $table->string('model_name')->nullable();
            $table->string('category_name')->default('Handphone');
            $table->string('previous_employee_number')->nullable();
            $table->string('previous_employee_name')->nullable();
            $table->string('status')->default('available'); // available, checked_out
            $table->unsignedBigInteger('assigned_to_user_id')->nullable();
            $table->string('assigned_to_snipeit_user_id')->nullable();
            $table->string('assigned_to_name')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resigned_assets');
    }
};
