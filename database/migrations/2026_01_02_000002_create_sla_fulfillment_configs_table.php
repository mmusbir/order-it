<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sla_fulfillment_configs', function (Blueprint $table) {
            $table->id();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->unique();
            $table->unsignedInteger('response_hours'); // Target response IT
            $table->unsignedInteger('fulfillment_hours'); // Target fulfillment
            $table->unsignedTinyInteger('warning_percent')->default(50);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default values based on user's SLA guidelines
        // urgent=P1, high=P2, medium=P3, low=P4
        DB::table('sla_fulfillment_configs')->insert([
            ['priority' => 'urgent', 'response_hours' => 1, 'fulfillment_hours' => 8, 'warning_percent' => 50, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['priority' => 'high', 'response_hours' => 4, 'fulfillment_hours' => 24, 'warning_percent' => 50, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['priority' => 'medium', 'response_hours' => 8, 'fulfillment_hours' => 40, 'warning_percent' => 50, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['priority' => 'low', 'response_hours' => 16, 'fulfillment_hours' => 72, 'warning_percent' => 50, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sla_fulfillment_configs');
    }
};
