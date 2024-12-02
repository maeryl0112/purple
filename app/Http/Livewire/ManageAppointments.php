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

    public $confirmAppointmentCancellation  = false;
    public $confirmingAppointmentCancellation = false;

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


    public function confirmAppointmentCancellation() {
        $this->confirmingAppointmentCancellation = true;
    }



    public function cancelAppointment(Appointment $appointment)
    {
        $this->appointment = $appointment;


        if (auth()->user()->id == $this->appointment->user->id
            || auth()->user()->role->name == (UserRolesEnum::Employee->name || UserRolesEnum::Admin->name)) {

            $this->appointment->status = 0;
//        $this->appointment->cancelled_by = auth()->user()->id;
            // TODO add reason
            $this->appointment->save();
            $this->confirmingAppointmentCancellation = false;
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
    if (!$this->paymentType) {
        $this->errorMessage = 'Please select a payment method.';
        return;
    }

    $appointment = Appointment::find($this->appointmentId);

    if ($appointment) {
        $appointment->status = 2;
        $appointment->payment = $this->paymentType;
        $appointment->save();

        $this->reset(['showPaymentModal', 'paymentType', 'appointmentId', 'errorMessage']);

        session()->flash('message', 'Appointment marked as completed.');
    } else {
        session()->flash('error', 'Appointment not found.');
    }
}


}
