<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentCancellationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Appointment $appointment)
    {
     
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail','database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('Your Appointment Has Been Canceled')
                    ->line('We regret to inform you that your appointment for the' . $this->appointment->service->name . 'service with' . $this->first_name . 'on' . $this->date . 'at' . $this->time .  'has been canceled.')
                    ->line('Reason: '. $this->cancellation_reason)
                    ->line('We apologize for the inconvenience.')
                    ->line('If you have any questions, please contact us.')
                    ->action('View Appointments', route('dashboard'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => "Your appointment for the {$this->serviceName} service with {$this->staffName} on {$this->date} at {$this->time} has been canceled. Reason: {$this->reason}.",
            'action_url' => route('dashboard'),
        ];
    }
}
