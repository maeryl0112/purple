<?php

namespace App\Http\Livewire;

use App\Models\Appointment;
use App\Models\TimeSlot;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use App\Events\AppointmentCancelled;
use App\Jobs\SendReschedAppointmentMailJob;
use App\Notifications\ReschuledAppointmentNotification;
use Livewire\WithPagination;


class CustomerViewAppointment extends Component
{
    use WithPagination;
    public $search;
    public $selectFilter = 'upcoming';
    public $confirmingAppointmentCancellation = false;
    public $confirmingAppointmentEdit = false;
    public $cancellationReason;
    public $appointmentIdToCancel;
    public $editingAppointment = false;
    public $selectedAppointment;
    public $newDate;
    public $newTimeSlot;
    public $appointment;
    public $availableTimeSlots = []; // Declare the property here
    private $timeNow;

    protected $rules = [
        "appointment.service_id" => "required|integer",
        "appointment.date" => "required|date",
        "appointment.time" => "required|integer",
    ];

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function mount()
    {
        $this->timeNow = Carbon::now();
    }


    public function render()
    {
        $query = Appointment::with( 'user', 'service')
            ->where('user_id', auth()->user()->id);

        if ($this->search) {
            $query->where(function ($subQuery) {
                $subQuery
                    ->where('date', 'like', '%' . $this->search . '%')
                    ->orWhere('appointment_code', 'like', '%' . $this->search . '%')
                    ->orWhereHas('service', function ($serviceQuery) {
                        $serviceQuery->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->selectFilter === 'previous') {
            $query->whereDate('date', '<', Carbon::today())->where('status', 1);
        } elseif ($this->selectFilter === 'upcoming') {
            $query->whereDate('date', '>=', Carbon::today())->where('status', 1);
        } elseif ($this->selectFilter === 'cancelled') {
            $query->where('status', 0);
        } elseif ($this->selectFilter === 'completed') {
            $query->where('status',2);
        }

        $appointments = $query->orderBy('date')->orderBy('employee_id')->paginate(10);

        return view('livewire.customer-view-appointment', [
            'appointments' => $appointments,
        ]);
    }


    public function setAppointmentIdToCancel($id)
    {
        $this->appointmentIdToCancel = $id;
        $this->confirmingAppointmentCancellation = true;
    }
    use App\Events\AppointmentCancelled;

    public function cancelAppointment()
    {
        $this->validate([
            'cancellationReason' => 'required|string|max:255',
        ]);

        // Retrieve the appointment using the ID
        $appointment = Appointment::find($this->appointmentIdToCancel);

        // Check if appointment exists and belongs to the authenticated user
        if (!$appointment || auth()->user()->id !== $appointment->user_id) {
            session()->flash('error', 'Unauthorized or Appointment not found.');
            return;
        }

        // Update appointment status and cancellation reason
        $appointment->status = 0; // Assuming 0 means canceled
        $appointment->cancellation_reason = $this->cancellationReason;

        if ($appointment->save()) {
            // Broadcast event to notify admin and employee
            AppointmentCancelled::dispatch($appointment);

            // Successfully canceled, reset component state
            $this->reset(['confirmingAppointmentCancellation', 'cancellationReason', 'appointmentIdToCancel']);
            session()->flash('message', 'Appointment canceled successfully with reason: ' . $this->cancellationReason);
        } else {
            session()->flash('error', 'An error occurred while canceling the appointment.');
        }
    }
}
