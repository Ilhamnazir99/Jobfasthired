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
    Schema::table('jobs', function (Blueprint $table) {
        $table->unsignedBigInteger('job_category_id')->nullable()->after('employer_id');
        $table->foreign('job_category_id')->references('id')->on('job_categories')->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('jobs', function (Blueprint $table) {
        $table->dropForeign(['job_category_id']);
        $table->dropColumn('job_category_id');
    });
}

};
