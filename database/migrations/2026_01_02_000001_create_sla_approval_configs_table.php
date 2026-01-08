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
        Schema::create('sla_approval_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('approval_level')->unique(); // 1-4
            $table->unsignedInteger('target_hours'); // Target jam kerja
            $table->unsignedTinyInteger('warning_percent')->default(50); // Threshold warning
            $table->unsignedTinyInteger('escalation_percent')->default(80); // Threshold escalation
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed default values
        DB::table('sla_approval_configs')->insert([
            ['approval_level' => 1, 'target_hours' => 4, 'warning_percent' => 50, 'escalation_percent' => 80, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['approval_level' => 2, 'target_hours' => 8, 'warning_percent' => 50, 'escalation_percent' => 80, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['approval_level' => 3, 'target_hours' => 8, 'warning_percent' => 50, 'escalation_percent' => 80, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['approval_level' => 4, 'target_hours' => 8, 'warning_percent' => 50, 'escalation_percent' => 80, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sla_approval_configs');
    }
};
