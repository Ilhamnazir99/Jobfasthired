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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employer_id'); // FK to users table
            $table->string('title');
            $table->text('description');
            $table->string('location'); // e.g. "Kuala Lumpur"
            $table->decimal('salary', 8, 2)->nullable();
            $table->string('schedule')->nullable(); // e.g. "Weekends", "9AM–5PM"
            $table->string('category')->nullable(); // e.g. "Retail", "F&B"
            $table->double('latitude')->nullable(); // For map
            $table->double('longitude')->nullable(); // For map
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
