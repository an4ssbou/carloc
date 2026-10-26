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
        Schema::create('agencies', function (Blueprint $table) {
            $table->id();
            
            $table->string('name');
            $table->string('slug')->unique(); 
            $table->string('city');
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            
            $table->foreignId('founder_id')
                  ->constrained('users')
                  ->onDelete('restrict');
            
            $table->string('logo')->nullable();
            $table->text('description')->nullable();
            
            $table->enum('status', ['pending', 'active', 'rejected', 'suspended'])
                  ->default('pending');
            
            $table->text('rejection_reason')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            
            $table->unsignedInteger('total_vehicles')->default(0);
            $table->unsignedInteger('total_reservations')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agencies');
    }
};
