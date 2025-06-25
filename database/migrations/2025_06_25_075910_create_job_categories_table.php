<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('job_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('order_by')->default(0); // ← Tambahan untuk sorting
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_categories');
    }
};
