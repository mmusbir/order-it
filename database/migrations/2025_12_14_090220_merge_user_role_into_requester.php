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
        // Step 1: Update all users with role 'user' to 'requester'
        DB::table('users')
            ->where('role', 'user')
            ->update(['role' => 'requester']);

        // Step 2: Deactivate the 'user' role in roles table (if exists)
        DB::table('roles')
            ->where('slug', 'user')
            ->update(['is_active' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reactivate the 'user' role
        DB::table('roles')
            ->where('slug', 'user')
            ->update(['is_active' => true]);

        // Note: Cannot restore individual users back to 'user' role automatically
    }
};
