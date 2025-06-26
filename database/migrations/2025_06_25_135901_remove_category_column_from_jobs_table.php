<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveCategoryColumnFromJobsTable extends Migration
{
    public function up()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('category'); // ✅ drop old column
        });
    }

    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->string('category')->nullable(); // ✅ add back if rolled back
        });
    }
}
