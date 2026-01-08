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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->integer('approval_level')->nullable(); // 1=SPV, 2=Manager, 3=Head, 4=Director
            $table->boolean('is_approver')->default(false);
            $table->boolean('is_system')->default(false); // System roles cannot be deleted
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default system roles
        DB::table('roles')->insert([
            ['name' => 'User (Requester)', 'slug' => 'user', 'description' => 'Can create and track IT asset requests', 'approval_level' => null, 'is_approver' => false, 'is_system' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Supervisor', 'slug' => 'spv', 'description' => 'Approver Level 1 - First to review and approve requests', 'approval_level' => 1, 'is_approver' => true, 'is_system' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Manager', 'slug' => 'manager', 'description' => 'Approver Level 2 - Reviews SPV-approved requests', 'approval_level' => 2, 'is_approver' => true, 'is_system' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Head', 'slug' => 'head', 'description' => 'Approver Level 3 - Reviews manager-approved requests', 'approval_level' => 3, 'is_approver' => true, 'is_system' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Director', 'slug' => 'director', 'description' => 'Approver Level 4 - Final approval before PO is issued', 'approval_level' => 4, 'is_approver' => true, 'is_system' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'IT Admin - Manages catalog, generates PO, tracks delivery', 'approval_level' => null, 'is_approver' => false, 'is_system' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Superadmin', 'slug' => 'superadmin', 'description' => 'Full system access - Can manage users, settings, and all data', 'approval_level' => null, 'is_approver' => false, 'is_system' => true, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
