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
        // Modify enum to include 'urgent' option
        DB::statement("ALTER TABLE requests MODIFY COLUMN priority ENUM('low', 'medium', 'high', 'urgent') DEFAULT 'low'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE requests MODIFY COLUMN priority ENUM('low', 'medium', 'high') DEFAULT 'medium'");
    }
};
