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
        Schema::create('approval_role_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approval_role_id')->constrained('approval_roles')->onDelete('cascade');
            $table->unsignedTinyInteger('level')->comment('Approval level (1-10)');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['approval_role_id', 'level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_role_levels');
    }
};
