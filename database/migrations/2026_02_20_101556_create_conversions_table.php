<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversions', function (Blueprint $table) {
            $table->id();
            $table->char('click_id', 36);
            $table->decimal('revenue', 15, 2)->nullable();
            $table->char('currency', 3)->nullable();
            $table->string('order_id', 255)->nullable();
            $table->dateTime('ym_sent_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('click_id')
                ->references('click_id')
                ->on('clicks')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversions');
    }
};
