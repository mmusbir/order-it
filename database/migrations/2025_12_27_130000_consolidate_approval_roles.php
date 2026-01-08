<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * Consolidate spv, manager, head, director roles into single 'approver' role
     */
    public function up(): void
    {
        // First, update all users with old roles to 'approver'
        DB::table('users')
            ->whereIn('role', ['spv', 'manager', 'head', 'director'])
            ->update(['role' => 'approver']);

        // Check if 'approver' role exists in roles table, if not create it
        $approverExists = DB::table('roles')->where('slug', 'approver')->exists();

        if (!$approverExists) {
            DB::table('roles')->insert([
                'name' => 'Approver',
                'slug' => 'approver',
                'description' => 'User with approval permissions. Approval level is determined by ApprovalRoleLevel mappings.',
                'is_active' => true,
                'is_system' => true,
                'approval_level' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Delete or deactivate old roles
        DB::table('roles')
            ->whereIn('slug', ['spv', 'manager', 'head', 'director'])
            ->delete();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration cannot be safely reversed automatically
        // You would need to restore the old roles and reassign users
    }
};
