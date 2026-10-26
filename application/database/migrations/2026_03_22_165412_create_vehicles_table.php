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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();

            $table->foreignId('agency_id')
                  ->constrained('agencies')
                  ->onDelete('cascade');

            $table->string('brand');
            $table->string('model');
            $table->unsignedSmallInteger('year');
            $table->string('registration_number')->unique();
            $table->unsignedInteger('mileage')->default(0);
            $table->string('color');
            $table->enum('condition', ['new', 'good', 'used'])->default('good');
            $table->unsignedTinyInteger('seats')->default(5);
            $table->enum('fuel_type', ['gasoline', 'diesel', 'electric', 'hybrid'])->default('gasoline');
            $table->enum('transmission', ['manual', 'automatic'])->default('manual');
            $table->text('description')->nullable();

            $table->boolean('has_gps')->default(false);
            $table->decimal('gps_latitude', 10, 7)->nullable();
            $table->decimal('gps_longitude', 10, 7)->nullable();

            $table->decimal('price_per_day', 10, 2);      
            $table->decimal('price_week', 10, 2)->nullable();
            $table->decimal('price_two_weeks', 10, 2)->nullable();
            $table->decimal('price_month', 10, 2)->nullable();

            $table->enum('status', ['available', 'unavailable'])->default('available');
            $table->boolean('is_manually_unavailable')->default(false);
            $table->boolean('is_featured')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->index(['agency_id', 'status']);
            $table->index(['brand', 'status']);
        });

        Schema::create('vehicle_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')
                  ->constrained('vehicles')
                  ->onDelete('cascade');
            $table->string('path');
            $table->boolean('is_primary')->default(false);
            $table->unsignedTinyInteger('order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicle_photos');
        Schema::dropIfExists('vehicles');
    }
};
