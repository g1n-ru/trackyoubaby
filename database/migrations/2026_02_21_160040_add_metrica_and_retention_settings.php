<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->insert([
            [
                'key' => 'ym_counter_id',
                'value' => env('YM_COUNTER_ID', ''),
                'type' => 'string',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'ym_token',
                'value' => env('YM_TOKEN', ''),
                'type' => 'string',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'data_retention_days',
                'value' => (string) env('DATA_RETENTION_DAYS', 90),
                'type' => 'integer',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', [
            'ym_counter_id',
            'ym_token',
            'data_retention_days',
        ])->delete();
    }
};
