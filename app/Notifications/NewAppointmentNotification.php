<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class NewAppointmentNotification extends Notification
{
    use Queueable;

    public $appointment;

    /**
     * Create a new notification instance.
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function via($notifiable)
    {
        return ['database'];
    }


    public function toDatabase($notifiable)
    {
     
        $paymentMethod = $this->appointment->payment ?? 'N/A';
        $paymentDetails = $paymentMethod === 'online' && $this->appointment->last_four_digits
            ? ' (Reference no. ending in ' . $this->appointment->last_four_digits . ')'
            : '';
    
        return [
            'appointment_id' => $this->appointment->id,
            'user_name' => $this->appointment->user->name ?? 'Unknown User',
            'payment_method' => $paymentMethod,
            'employee_name' => $this->appointment->first_name ?? 'Unassigned Employee', // 👈 Add assigned employee
            'branch_id' => $this->appointment->employee->branch_id ?? 'Unknown Branch', // 👈 Add employee's branch
            'message' => 'A new Appointment has been booked by ' 
                . ($this->appointment->user->name ?? 'Unknown User') 
                . ' with Payment Method: ' 
                . $paymentMethod
                . $paymentDetails
                . ' - Assigned to: ' . ($this->appointment->first_name ?? 'Unassigned Employee')
                . ' at Branch: ' . ($this->appointment->employee->branch->name ?? 'Unknown Branch'),
        ];
    }
    
}
