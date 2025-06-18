<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade'); // Student applying
            $table->foreignId('job_id')->constrained('jobs')->onDelete('cascade'); // Job being applied
            $table->string('status')->default('Pending'); // Application status (Pending, Approved, Rejected)
            $table->text('message')->nullable(); // Optional message from student
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('applications');
    }
}
