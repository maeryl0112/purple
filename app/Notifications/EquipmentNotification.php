<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EquipmentNotification extends Notification
{
    use Queueable;
    protected $equipment;
    protected $type;

    public function __construct($equipment, $type)
    {
        $this->equipment = $equipment;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject('Equipment Notification: ' . ($this->type === 'low_quantity' ? 'Low Quantity' : 'Maintenance Due'))
            ->line('Equipment: ' . $this->equipment->name);

        if ($this->type === 'low_quantity') {
            $message->line('Quantity is low: ' . $this->equipment->quantity);
        } elseif ($this->type === 'maintenance_due') {
            $message->line('Next maintenance due on: '. $this->equipment->next_maintenance);
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
            'equipment_id' => $this->equipment->id,
            'equipment_name' => $this->equipment->name,
            'type' => $this->type,
            'message' => $this->type === 'low_quantity' 
                ? 'Quantity is low: ' . $this->equipment->quantity
                : 'Next maintenance due on: ' . $this->equipment->next_maintenance,
        ];
    }
}
