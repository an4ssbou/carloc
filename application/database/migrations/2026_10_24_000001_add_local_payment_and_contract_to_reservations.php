<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->string('payment_status')->default('not_simulated');
            $table->string('payment_reference')->nullable()->unique();
            $table->timestamp('payment_simulated_at')->nullable();
            $table->json('contract_snapshot')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropUnique(['payment_reference']);
            $table->dropColumn(['payment_status', 'payment_reference', 'payment_simulated_at', 'contract_snapshot']);
        });
    }
};
