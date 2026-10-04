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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('specialization');
            $table->string('email');
            $table->string('phone');
            $table->text('bio')->nullable();
            $table->decimal('consultation_fee', 8, 2)->default(50.00);
            $table->string('room_number')->nullable();
            $table->json('available_days')->nullable();
            $table->string('start_time')->default('09:00');
            $table->string('end_time')->default('17:00');
            $table->integer('slot_duration_minutes')->default(30);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
