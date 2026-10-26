<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('gps_token_hash', 64)->nullable();
            $table->timestamp('gps_recorded_at', 6)->nullable();
            $table->timestamp('gps_received_at', 6)->nullable();
            $table->string('gps_source', 16)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['gps_token_hash', 'gps_recorded_at', 'gps_received_at', 'gps_source']);
        });
    }
};
