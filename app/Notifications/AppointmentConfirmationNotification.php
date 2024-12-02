<?php

namespace App\Notifications;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentConfirmationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Appointment $appointment
    )
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject( 'Appointment Confirmation - Purple Look Hair Salon and Spa 🎉' . $this->appointment->service->name)
            ->from('noreply@purplelooksalonandspa.com')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your appointment for ' . $this->appointment->service->name . ' has been confirmed!')
            ->line('Your total is ' . $this->appointment->total . '.')
            ->line('🧾 Appointment Code: ' . $this->appointment->appointment_code)
            ->line('📅 Date: ' . $this->appointment->date)
            ->line('⏰ Time: ' . $this->appointment->time)
            ->line('📞 Staff Assigned: ' . $this->appointment->employee->first_name)

            ->action(
                'View Your Appointment',
                route('customerview', ['customer' => $this->appointment->user->id]) . '?search=' . $this->appointment->appointment_code
            )
            ->line('Thank you for choosing Purple Look Hair Salon and Spa! We look forward to see you soon.')
            ->line('*Strictly no late, 10 minutes grace period.')
            ->line('If you have any questions about your appointment.')
            ->line('Please contact 09******** or email purplelookhairsalonandspa@gmail.com');

    }

    public function toArray($notifiable): array
    {
        return [];
    }
}
