<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use App\Jobs\SendAppointmentReminder;
use Carbon\Carbon;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for upcoming appointments';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get current time and the next hour
        $now = Carbon::now();
        $nextHour = $now->copy()->addHour();

        // Retrieve appointments scheduled within the next hour
        $appointments = Appointment::where('date', '=', $now->toDateString())
            ->whereBetween('time', [$now->toTimeString(), $nextHour->toTimeString()])
            ->where('status', 2) // Assuming 2 means confirmed
            ->get();

        if ($appointments->isEmpty()) {
            $this->info('No appointments to remind at this time.');
            return 0;
        }

        foreach ($appointments as $appointment) {
            SendAppointmentReminder::dispatch($appointment);
            $this->info('Reminder queued for Appointment ID: ' . $appointment->id);
        }

        return 0;
    }
}
