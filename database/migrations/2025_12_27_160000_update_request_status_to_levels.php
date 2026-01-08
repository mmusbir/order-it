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
        // Update requests status
        DB::table('requests')->where('status', 'APPR_SPV')->update(['status' => 'APPR_1']);
        DB::table('requests')->where('status', 'APPR_MGR')->update(['status' => 'APPR_2']);
        DB::table('requests')->where('status', 'APPR_HEAD')->update(['status' => 'APPR_3']);
        DB::table('requests')->where('status', 'APPR_DIR')->update(['status' => 'APPR_4']);

        // Update approval logs roles if they use old role names
        // Ideally roles in logs should be LEVEL_1, LEVEL_2, etc.
        // But if they were SPV, MANAGER, etc., let's update them too for consistency if needed.
        // Assuming current codebase uses LEVEL_X format, but checking old logs just in case.
        DB::table('approval_logs')->where('role', 'SPV')->update(['role' => 'LEVEL_1']);
        DB::table('approval_logs')->where('role', 'MANAGER')->update(['role' => 'LEVEL_2']);
        DB::table('approval_logs')->where('role', 'HEAD')->update(['role' => 'LEVEL_3']);
        DB::table('approval_logs')->where('role', 'DIRECTOR')->update(['role' => 'LEVEL_4']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('requests')->where('status', 'APPR_1')->update(['status' => 'APPR_SPV']);
        DB::table('requests')->where('status', 'APPR_2')->update(['status' => 'APPR_MGR']);
        DB::table('requests')->where('status', 'APPR_3')->update(['status' => 'APPR_HEAD']);
        DB::table('requests')->where('status', 'APPR_4')->update(['status' => 'APPR_DIR']);

        DB::table('approval_logs')->where('role', 'LEVEL_1')->update(['role' => 'SPV']);
        DB::table('approval_logs')->where('role', 'LEVEL_2')->update(['role' => 'MANAGER']);
        DB::table('approval_logs')->where('role', 'LEVEL_3')->update(['role' => 'HEAD']);
        DB::table('approval_logs')->where('role', 'LEVEL_4')->update(['role' => 'DIRECTOR']);
    }
};
