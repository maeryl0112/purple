<?php

namespace App\View\Components;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DaySchedule extends Component
{
    public $daySchedule;
    public $date;

    public function __construct(Carbon $date)
    {
        $this->date = $date;

        // Fetch the day's schedule based on date
        $this->daySchedule = $this->getDaySchedule();
    }

    public function render(): View
    {
        // Ensure that daySchedule is passed to the view
        return view('components.day-schedule', ['daySchedule' => $this->daySchedule]);
    }

    private function getDaySchedule()
    {
        return Appointment::where('date', $this->date->toDateString())
            ->where('status', '!=', 0) // Filter out canceled appointments
            ->orderBy('time', 'asc')
            ->with('service', 'employee', 'user')
            ->get();
    }
}

