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
            // Add custom item fields (product_id already nullable via default)
            $table->string('item_name')->nullable()->after('product_id');
            $table->string('item_specs')->nullable()->after('item_name');
            $table->string('item_link')->nullable()->after('snap_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_items', function (Blueprint $table) {
            $table->dropColumn(['item_name', 'item_specs', 'item_link']);
        });
    }
};
