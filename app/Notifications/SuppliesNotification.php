<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SuppliesNotification extends Notification
{
    use Queueable;

    protected $supply;
    protected $type;

    /**
     * Create a new notification instance.
     */
    public function __construct($supply, $type)
    {
        $this->supply = $supply;
        $this->type = $type;
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
    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject('Consumables Notification: ' . ($this->type === 'low_quantity' ? 'Low Quantity' : 'Near Expiration'))
            ->line('Consumable: ' . $this->supply->name);

        if ($this->type === 'low_quantity') {
            $message->line('Quantity is low: ' . $this->supply->quantity);
        } elseif ($this->type === 'near_expiration') {
            $message->line('Near expiration date on: ' . $this->supply->expiration_date);
        }

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'supply_id' => $this->supply->id,
            'supply_name' => $this->supply->name,
            'type' => $this->type,
            'message' => $this->type === 'low_quantity'
                ? 'Quantity is low: ' . $this->supply->quantity
                : 'Near expiration on: ' . 
                $this->supply->expiration_date,
        ];
    }
}
