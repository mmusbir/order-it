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
        Schema::table('products', function (Blueprint $table) {
            // Remove price column if exists
            if (Schema::hasColumn('products', 'price')) {
                $table->dropColumn('price');
            }

            // Add Snipe-IT integration columns
            if (!Schema::hasColumn('products', 'category')) {
                $table->string('category')->nullable()->after('specs');
            }
            if (!Schema::hasColumn('products', 'model_name')) {
                $table->string('model_name')->nullable()->after('category');
            }
            if (!Schema::hasColumn('products', 'snipeit_category_id')) {
                $table->unsignedInteger('snipeit_category_id')->nullable()->after('snipeit_model_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'price')) {
                $table->decimal('price', 15, 2)->default(0);
            }
            if (Schema::hasColumn('products', 'category')) {
                $table->dropColumn('category');
            }
            if (Schema::hasColumn('products', 'model_name')) {
                $table->dropColumn('model_name');
            }
            if (Schema::hasColumn('products', 'snipeit_category_id')) {
                $table->dropColumn('snipeit_category_id');
            }
        });
    }
};
