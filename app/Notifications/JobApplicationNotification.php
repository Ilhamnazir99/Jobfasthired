<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class JobApplicationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data) // Accepts array for flexibility
    {
        $this->data = $data;
    }

    /**
     * Determine the delivery channels for the notification.
     */
    public function via($notifiable)
    {
        return ['mail', 'database']; // Send to both email and database (in-app)
    }

    /**
     * Define the email notification content.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Job Application Received')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line($this->data['student_name'] . ' has applied for the job position: ' . $this->data['job_title'] . '.')
            ->action('View Application', url('/employer/dashboard'))
            ->line('Thank you for using JobFastHired.');
    }

    /**
     * Define the database (in-app) notification content.
     */
    public function toDatabase($notifiable)
    {
        return [
            'student_name' => $this->data['student_name'],
            'job_title' => $this->data['job_title'],
            'job_id' => $this->data['job_id'],
            'message' => 'A new application has been submitted by ' . $this->data['student_name'],
        ];
    }

    /**
     * Define the array representation for broadcast (optional).
     */
    public function toArray($notifiable)
    {
        return [
            'student_name' => $this->data['student_name'],
            'job_title' => $this->data['job_title'],
            'job_id' => $this->data['job_id'],
        ];
    }
}
