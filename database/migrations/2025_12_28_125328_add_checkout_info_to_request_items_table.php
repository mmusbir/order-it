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
            $table->string('synced_item_name')->nullable()->after('snipeit_asset_id');
            $table->string('synced_location_name')->nullable()->after('synced_item_name');
            $table->integer('synced_qty')->nullable()->after('synced_location_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_items', function (Blueprint $table) {
            $table->dropColumn(['synced_item_name', 'synced_location_name', 'synced_qty']);
        });
    }
};
