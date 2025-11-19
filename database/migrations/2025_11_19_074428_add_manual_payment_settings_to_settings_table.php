<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert default manual payment settings
        DB::table('settings')->insert([
            [
                'key' => 'manual_payment_account_number',
                'value' => '0123456789',
            ],
            [
                'key' => 'manual_payment_bank_name',
                'value' => 'Sample Bank',
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove manual payment settings
        DB::table('settings')->whereIn('key', [
            'manual_payment_account_number',
            'manual_payment_bank_name',
        ])->delete();
    }
};
