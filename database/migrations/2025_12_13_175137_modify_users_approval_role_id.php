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
        Schema::table('users', function (Blueprint $table) {
            // Drop the old approval_level column if it exists
            if (Schema::hasColumn('users', 'approval_level')) {
                $table->dropColumn('approval_level');
            }

            // Add new approval_role_id column
            if (!Schema::hasColumn('users', 'approval_role_id')) {
                $table->foreignId('approval_role_id')->nullable()->after('role')->constrained('approval_roles')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'approval_role_id')) {
                $table->dropForeign(['approval_role_id']);
                $table->dropColumn('approval_role_id');
            }

            if (!Schema::hasColumn('users', 'approval_level')) {
                $table->unsignedTinyInteger('approval_level')->nullable()->after('role');
            }
        });
    }
};
