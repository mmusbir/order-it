<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
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
            $year = Carbon::parse($request->created_at)->format('Y');

            // Generate unique ticket number
            do {
                $ticketNo = 'BAV-' . $year . '-' . str_pad(mt_rand(0, 9999), 4, '0', STR_PAD_LEFT);
                $exists = DB::table('requests')->where('ticket_no', $ticketNo)->where('id', '!=', $request->id)->exists();
            } while ($exists);

            DB::table('requests')->where('id', $request->id)->update([
                'ticket_no' => $ticketNo
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No revert possible as data is overwritten randomly
    }
};
