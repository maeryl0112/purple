<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AvailableSlotNotification extends Notification
{
    use Queueable;

    public $date;
    public $time;
    public $staff;
    public $serviceName;


    /**
     * Create a new notification instance.
     */
    public function __construct($date, $time, $staff, $serviceName)
    {
        $this->date = $date;
        $this->time = $time;
        $this->staff = $staff;
        $this->serviceName = $serviceName;
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
                ->subject('New Appointment Slot Available')
                ->line('A slot for the' . $this->serviceName . 'service with' . $this->first_name . 'has become available.')
                ->line('Date: ' . $this->date)
                ->line('Time: ' . $this->time)
                ->action('Book Now', route('services'))
                ->line('Hurry up and book your slot before it gets taken!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => "A slot with . $this->first_name .  is available on  .$this->date . at " . $this->time,
            'action_url' => route('services'),
        ];
    }
}
