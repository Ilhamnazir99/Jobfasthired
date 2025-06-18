<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Determine which channels the notification will be sent through.
     */
    public function via($notifiable)
    {
        return ['mail', 'database']; // Sends both email and in-app notification
    }

    /**
     * Define the email content.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Job Application Status: ' . ucfirst($this->data['status']))
            ->greeting('Hi ' . $notifiable->name . ',')
            ->line('Your application for the job "' . $this->data['job_title'] . '" has been ' . strtolower($this->data['status']) . '.')
            ->action('View Job Application', url('/student/applied-jobs'))
            ->line('Thank you for using JobFastHired.');
    }

    /**
     * Define the database notification payload.
     */
    public function toDatabase($notifiable)
    {
        return [
            'job_id'    => $this->data['job_id'],
            'job_title' => $this->data['job_title'],
            'status'    => $this->data['status'], // accepted / rejected
            'message'   => 'Your application for "' . $this->data['job_title'] . '" was ' . strtolower($this->data['status']) . '.',
        ];
    }

    /**
     * Optionally define the broadcast payload.
     */
    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable); // Reuse same structure
    }
}
