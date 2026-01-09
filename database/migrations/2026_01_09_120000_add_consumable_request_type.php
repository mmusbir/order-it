<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if 'CONSUMABLE' request type already exists
        $exists = DB::table('request_types')->where('slug', 'CONSUMABLE')->exists();

        if (!$exists) {
            DB::table('request_types')->insert([
                'name' => 'Consumable',
                'slug' => 'CONSUMABLE',
                'description' => 'Request untuk barang habis pakai (consumable items)',
                'allow_quantity' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('request_types')->where('slug', 'CONSUMABLE')->delete();
    }
};
