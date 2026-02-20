<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clicks', function (Blueprint $table) {
            $table->id();
            $table->char('click_id', 36)->unique();
            $table->dateTime('ts_utc', 3);
            $table->string('ip', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('referer')->nullable();
            $table->string('subid', 255)->nullable()->index();
            $table->string('subid2', 255)->nullable();
            $table->string('subid3', 255)->nullable();
            $table->string('subid4', 255)->nullable();
            $table->text('landing_url')->nullable();
            $table->json('raw_query_json')->nullable();
            $table->string('ym_uid', 255)->nullable()->index();
            $table->dateTime('ym_sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clicks');
    }
};
