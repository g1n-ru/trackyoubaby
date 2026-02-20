<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metrica_send_logs', function (Blueprint $table) {
            $table->id();
            $table->char('click_id', 36)->index();
            $table->string('event_type', 50);
            $table->json('request_payload')->nullable();
            $table->unsignedSmallInteger('response_status')->nullable();
            $table->text('response_body')->nullable();
            $table->boolean('success')->default(false)->index();
            $table->unsignedTinyInteger('retry_count')->default(0);
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metrica_send_logs');
    }
};
