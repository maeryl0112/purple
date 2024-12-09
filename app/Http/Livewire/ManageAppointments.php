<?php

namespace App\Http\Livewire;

use App\Enums\UserRolesEnum;
use App\Models\Appointment;
use Carbon\Carbon;
use Livewire\Component;

class ManageAppointments extends Component
{

    private $appointments;
    public $showPaymentModal = false;
    public $paymentType = false;
    public $appointmentId;
    public $errorMessage;
    public $search;
    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public $appointment;

    public $confirmingAppointmentAdd;
    public $appointmentIdToCancel;
    public $confirmingAppointmentCancellation = false;
    public $cancellationReason;
    private $timeNow;

    public $selectFilter = 'upcoming'; // can be 'upcoming' , 'previous' , 'cancelled'
    public $paymentFilter = null;
    private $userId;


    public function openPaymentModal($id)
    {
        $this->showPaymentModal = true;
        $this->appointmentId = $id;
        $this->paymentType = null;
        $this->errorMessage = null;
    }
    public function closePaymentModal()
    {
        $this->reset(['showPaymentModal', 'paymentType', 'appointmentId', 'errorMessage']);
    }



    public function mount($userId = null, $selectFilter = 'upcoming') {

       if (auth()->user()->role->name == "Customer") {
            $this->userId = auth()->user()->id;
        } else if (auth()->user()->role->name == ("Employee" || "Admin")) {
           $this->userId = $userId;
        }
        $selectFilter ? $this->selectFilter = $selectFilter : $this->selectFilter = 'upcoming';

        $this->timeNow = Carbon::now();
    }

    public function render()
    {
        $query = Appointment::with( 'user', 'service');
        if ($this->search) {
            $query->where(function ($subQuery) {
                $subQuery
                    ->where('date', 'like', '%' . $this->search . '%')
                    ->orWhere('appointment_code', 'like', '%' . $this->search . '%')
                    ->orWhere('time', 'like', '%' . $this->search . '%')
                    ->orWhere('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('status', 'like', '%' . $this->search . '%')
                    ->orWhere('service_id', 'like', '%' . $this->search . '%');
            });

            $query->orWhereHas('user', function ($userQuery) {
                $userQuery->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('phone_number', 'like', '%' . $this->search . '%');
            });

            $query->orWhereHas('service', function ($serviceQuery) {
                $serviceQuery->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhere('category_id', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->paymentFilter) {
            $query->where('payment', $this->paymentFilter); // Filter by payment type
        }

        if ($this->userId) {

            $query->where('user_id', $this->userId);
        }
//        dd($this->selectFilter);
        if ($this->selectFilter === 'previous') {
            $query->whereDate('date', '<', Carbon::today())->where('status', 1);

        } else if ($this->selectFilter === 'upcoming') {
            $query->whereDate('date', '>=', Carbon::today())->where('status', 1);

        } else if ($this->selectFilter === 'cancelled') {
            $query->where('status', 0);
        } else if ($this->selectFilter === 'completed') {
            $query->where('status',2);
        }



        // Get the appointments
        $this->appointments = $query->orderBy('date')->paginate(20);
//        dd($this->appointments);

        return view('livewire.manage-appointments', [
            'appointments' => $this->appointments,
        ]);
    }


    public function setAppointmentIdToCancel($id)
    {
        $this->appointmentIdToCancel = $id;
        $this->confirmingAppointmentCancellation = true;
    }
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
            // Successfully canceled, reset component state
            $this->reset(['confirmingAppointmentCancellation', 'cancellationReason', 'appointmentIdToCancel']);
            session()->flash('message', 'Appointment canceled successfully with reason: ' . $this->cancellationReason);
        } else {
            session()->flash('error', 'An error occurred while canceling the appointment.');
        }
    }

    public function markAsRead($notificationId)
    {
        $notification = auth()->user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            $this->emit('notificationRead'); // Emit an event to update the notification count
        }
    }

    public function completeAppointment()
    {

        $appointment = Appointment::find($this->appointmentId);

        if ($appointment) {
            $appointment->status = 2;
            $appointment->save();

            $this->reset(['showPaymentModal', 'appointmentId', 'errorMessage']);

            session()->flash('message', 'Appointment marked as completed.');
        } else {
            session()->flash('error', 'Appointment not found.');
        }
    }


}
