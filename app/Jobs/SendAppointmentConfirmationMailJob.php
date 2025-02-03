<?php

namespace App\Jobs;

use App\Http\Controllers\DisplayDeal;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use App\Notifications\AppointmentConfirmationNotification;
use App\Notifications\NewServiceReleasedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentConfirmationMail;


class SendAppointmentConfirmationMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $customer, $appointment;

    public function __construct($customer, $appointment)
    {
        $this->customer = $customer;
        $this->appointment = $appointment;
    }

    public function handle(): void
    {

        Mail::to($this->customer->email)->send(new AppointmentConfirmationMail($this->appointment));
    }
}
