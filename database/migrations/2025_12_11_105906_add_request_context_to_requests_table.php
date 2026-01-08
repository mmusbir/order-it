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
        Schema::table('requests', function (Blueprint $table) {
            $table->enum('request_type', ['NEW_HIRE', 'NEW_BRANCH', 'REPLACEMENT'])->nullable()->after('status');
            $table->enum('replacement_reason', ['AGING', 'BROKEN', 'LOST'])->nullable()->after('request_type');
            $table->string('disposal_doc_path')->nullable()->after('replacement_reason');

            $table->enum('beneficiary_type', ['BRANCH', 'USER'])->nullable()->after('disposal_doc_path');
            $table->string('beneficiary_id')->nullable()->after('beneficiary_type'); // NIK or Branch Code
            $table->string('beneficiary_name')->nullable()->after('beneficiary_id');

            $table->text('shipping_address')->nullable()->after('beneficiary_name');
            $table->string('shipping_pic_name')->nullable()->after('shipping_address');
            $table->string('shipping_pic_phone')->nullable()->after('shipping_pic_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('requests', function (Blueprint $table) {
            $table->dropColumn([
                'request_type',
                'replacement_reason',
                'disposal_doc_path',
                'beneficiary_type',
                'beneficiary_id',
                'beneficiary_name',
                'shipping_address',
                'shipping_pic_name',
                'shipping_pic_phone'
            ]);
        });
    }
};
