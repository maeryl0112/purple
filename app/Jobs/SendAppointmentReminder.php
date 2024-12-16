<?php

namespace App\Jobs;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class SendAppointmentReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $appointment;
    private $twilioClient;

    /**
     * Create a new job instance.
     *
     * @param Appointment $appointment
     * @return void
     */
    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
        $sid = config('services.twilio.account_sid');
        $token = config('services.twilio.auth_token');
        $this->twilioClient = new Client($sid, $token);
    }

    /**
     * Execute the job.
     *
     * @throws TwilioException
     * @return void
     */
    public function handle()
    {
        try {
            $template = "Reminder: Your appointment is scheduled on %s at %s for %s. Appointment Code: %s. Please contact us if you have any questions.";
            $body = sprintf(
                $template,
                $this->appointment->date->format('F j, Y'),
                $this->appointment->time->format('h:i A'),
                $this->appointment->service->name,
                $this->appointment->appointment_code
            );

            $message = $this->twilioClient->messages->create(
                $this->appointment->user->phone_number,
                [
                    'from' => config('services.twilio.phone_number'),
                    'body' => $body,
                ]
            );

            Log::info('Appointment reminder sent: ' . $message->sid);
        } catch (TwilioException $e) {
            Log::error('Failed to send appointment reminder: ' . $e->getMessage());
        }
    }
}
