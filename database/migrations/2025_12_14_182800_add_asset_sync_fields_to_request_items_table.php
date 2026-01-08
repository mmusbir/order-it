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
        Schema::table('request_items', function (Blueprint $table) {
            $table->string('asset_tag')->nullable()->after('serial_number');
            $table->unsignedBigInteger('snipeit_asset_id')->nullable()->after('asset_tag');
            $table->timestamp('synced_at')->nullable()->after('is_synced');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_items', function (Blueprint $table) {
            $table->dropColumn(['asset_tag', 'snipeit_asset_id', 'synced_at']);
        });
    }
};
