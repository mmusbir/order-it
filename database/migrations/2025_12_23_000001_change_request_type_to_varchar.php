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
        // Change request_type from ENUM to VARCHAR to support dynamic request types
        DB::statement("ALTER TABLE `requests` MODIFY `request_type` VARCHAR(50) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to enum (note: this may fail if there are values not in the enum)
        DB::statement("ALTER TABLE `requests` MODIFY `request_type` ENUM('NEW_HIRE', 'NEW_BRANCH', 'REPLACEMENT') NULL");
    }
};
