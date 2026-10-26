<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('vehicle_id')
                  ->constrained('vehicles')
                  ->onDelete('restrict');

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('restrict');

            $table->foreignId('agency_id')
                  ->constrained('agencies')
                  ->onDelete('restrict');

            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedSmallInteger('duration_days');

            $table->decimal('price_applied', 10, 2);
            $table->decimal('total_price', 10, 2);

            $table->enum('status', [
                'pending',
                'confirmed',
                'cancelled',
                'completed',
                'rejected'
            ])->default('pending');

            $table->text('client_notes')->nullable();       
            $table->text('cancellation_reason')->nullable();
            $table->string('cancelled_by')->nullable();

            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['vehicle_id', 'start_date', 'end_date']);
            $table->index(['vehicle_id', 'status']);
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
