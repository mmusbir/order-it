<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $requests = DB::table('requests')->orderBy('id')->get();

        foreach ($requests as $request) {
            // Check if format is BAV-YYYY-XXXX
            // We want BAV-YYYYXXXX

            // We can just regenerate to be safe and consistent with the new requirement
            // Or strictly remove the dash if it matches the pattern

            // Let's regenerate to ensure uniqueness (though removing dash from unique IDs usually keeps them unique unless extremely unlucky edge cases with different years/randoms overlapping, which shouldn't happen here)
            // But user said "gabung saja".
            // Old: BAV-2025-1234
            // New: BAV-20251234

            // Simple string replacement is faster and preferred if possible
            if (preg_match('/^BAV-(\d{4})-(\d{4})$/', $request->ticket_no, $matches)) {
                $newTicket = 'BAV-' . $matches[1] . $matches[2];

                // Uniqueness check (just in case)
                if (DB::table('requests')->where('ticket_no', $newTicket)->where('id', '!=', $request->id)->exists()) {
                    // Collision? Regenerate complete new one
                    $year = Carbon::parse($request->created_at)->format('Y');
                    do {
                        $newTicket = 'BAV-' . $year . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
                    } while (DB::table('requests')->where('ticket_no', $newTicket)->exists());
                }

                DB::table('requests')->where('id', $request->id)->update([
                    'ticket_no' => $newTicket
                ]);
            } else {
                // For records that might be in old BAV-YYYYMMDD-XXXXX format (if any missed) or already in new format
                // We apply regeneration logic for safety if it doesn't match expected target format
                if (!preg_match('/^BAV-(\d{4})(\d{4})$/', $request->ticket_no)) {
                    $year = Carbon::parse($request->created_at)->format('Y');
                    do {
                        $newTicket = 'BAV-' . $year . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
                    } while (DB::table('requests')->where('ticket_no', $newTicket)->where('id', '!=', $request->id)->exists());

                    DB::table('requests')->where('id', $request->id)->update([
                        'ticket_no' => $newTicket
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No revert
    }
};
