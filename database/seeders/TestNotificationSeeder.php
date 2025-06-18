<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Notification;
use App\Notifications\JobApplicationNotification; // Make sure this notification exists

class TestNotificationSeeder extends Seeder
{
    public function run()
    {
        // Get an example employer (Make sure you have an employer user)
        $employer = User::where('role', 'employer')->first();
        
        if ($employer) {
            // Send a test notification to the employer
            Notification::send($employer, new JobApplicationNotification([
                'student_name' => 'Test Student',
                'job_title' => 'Test Job Position',
                'job_id' => 1 // Make sure this job exists in your database
            ]));

            $this->command->info('Test notification sent to the employer.');
        } else {
            $this->command->error('No employer found. Make sure you have an employer user.');
        }
    }
}
