<?php

namespace App\Http\Livewire;

use App\Enums\UserRolesEnum;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\Employee;
use App\Models\Branch;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use App\Jobs\SendAppointmentConfirmationMailJob;
use App\Notifications\AppointmentConfirmationNotification;

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
    private $timeNow;

    public $confirmingAppointmentCancellation = false;
    public $appointmentIdToCancel;
    public $cancellationReason;

    public $selectFilter = 'upcoming'; // can be 'upcoming' , 'previous' , 'cancelled'
    public $paymentFilter = null;
    public $timeFilter = null;
    private $userId;
    public  $employeeId;
    public $serviceId;
    public $branchId;

    public $showRescheduleModal = false;
    public $newDate;
    public $newTime;

    public $filterDate = ''; 
    public $filterStaff = '';

   

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
    
        $appointment = Appointment::find($this->appointmentIdToCancel);
    
        if (!$appointment) {
            $this->dispatchBrowserEvent('appointmentError', [
                'message' => 'Appointment not found.',
            ]);
            return;
        }
    
        // Cancel the appointment
        $appointment->update([
            'status' => 0,
            'cancellation_reason' => $this->cancellationReason,
        ]);
    
        // Notify the customer who owns the canceled appointment
        $customer = $appointment->user; // Assuming 'user' relation exists
        $this->dispatchBrowserEvent('sweetAlert', [
            'title' => 'Appointment Canceled!',
            'text' => "Dear {$customer->name}, your appointment for {$appointment->service->name} on {$appointment->date} at {$appointment->time} has been canceled.",
            'icon' => 'info',
        ]);
        event(new SlotAvailable($appointment));
    
        // Notify all other customers about the slot availability
        $this->notifyOtherCustomers($appointment);
    
        $this->confirmingAppointmentCancellation = false;
    
        $this->dispatchBrowserEvent('notification', [
            'message' => 'Appointment canceled, and employee availability updated.',
        ]);
    }

    private function notifyOtherCustomers($appointment)
{
    $otherCustomers = User::where('id', '!=', $appointment->user_id)->get();

    foreach ($otherCustomers as $customer) {
        $this->dispatchBrowserEvent('notifySlotAvailable', [
            'message' => "A slot is now available for {$appointment->service->name} on {$appointment->date} at {$appointment->time}.",
            'customerName' => $customer->name,
        ]);
    }
}



    public function openRescheduleModal($appointmentId)
{
    $this->appointmentId = $appointmentId;
    $this->showRescheduleModal = true;

    // Optionally, load existing appointment data
    $appointment = Appointment::find($appointmentId);
    if ($appointment) {
        $this->newDate = $appointment->date;
        $this->newTime = $appointment->time;
    }
}

public function closeRescheduleModal()
{
    $this->reset(['showRescheduleModal', 'appointmentId', 'newDate', 'newTime']);
}

public function rescheduleAppointment()
{
    $this->validate([
        'newDate' => 'required|date|after:today',
        'newTime' => 'required|date_format:H:i',
    ]);

    $appointment = Appointment::find($this->appointmentId);

    if ($appointment) {
        $appointment->update([
            'date' => $this->newDate,
            'time' => $this->newTime,
        ]);

        $this->dispatchBrowserEvent('rescheduleSuccess');
        $this->closeRescheduleModal();
    } else {
        $this->dispatchBrowserEvent('rescheduleError');
    }
}

public function openPaymentModal($id)
{
    $this->showPaymentModal = true;
    $this->appointmentId = $id; // This ensures the ID is set
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
    $query = Appointment::with('user', 'service', 'employee');

    // Restrict employees to only their assigned branch
    if (auth()->user()->role_id === 2) {
        $query->whereHas('employee', function ($employeeQuery) {
            $employeeQuery->where('branch_id', auth()->user()->branch_id);
        });
    }
    
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

        if ($this->filterDate) {
            $query->whereDate('date', '=', $this->filterDate);
        }
        
        if ($this->paymentFilter) {
            $query->where('payment', $this->paymentFilter); // Filter by payment type
        }
        if ($this->timeFilter) {
            $query->where('time', $this->timeFilter); // Filter by payment type
        }

        if ($this->userId) {

            $query->where('user_id', $this->userId);
        }

        if ($this->employeeId) { // Add employee filter
            $query->where('employee_id', $this->employeeId);
        }
        
        if ($this->serviceId) { // Add service filter
            $query->where('service_id', $this->serviceId);
        }
        if ($this->branchId) { // Add service filter
            $query->where('branch_id', $this->branchId);
        }

    if ($this->selectFilter === 'previous') {
        $query->whereDate('date', '<', Carbon::today())->where('status', 1);
    } else if ($this->selectFilter === 'upcoming') {
        $query->whereDate('date', '>=', Carbon::today())->where('status', 1);
    } else if ($this->selectFilter === 'cancelled') {
        $query->where('status', 0);
    } else if ($this->selectFilter === 'completed') {
        $query->where('status', 2);
    }

    $this->appointments = $query->orderBy('created_at')->paginate(20);

    return view('livewire.manage-appointments', [
        'appointments' => $this->appointments,
        'employees' => Employee::where('branch_id', auth()->user()->branch_id)->get(),
        'services' => Service::all(),
        'branches' => Branch::all(),
    ]);
}

    public function markAsRead($notificationId)
    {
        $notification = auth()->user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            $this->emit('notificationRead'); // Emit an event to update the notification count
        }
    }

    public function completeAppointment($appointmentId)
{
     $appointment = Appointment::find($this->appointmentId);

    if ($appointment) {
        $appointment->status = 1;
        $appointment->save();

        // Get the specific customer
        $customer = User::find($appointment->user_id);

        if ($customer) {
            // Send the notification
            $customer->notify(new AppointmentConfirmationNotification($appointment));
        }

        $this->reset(['showPaymentModal', 'appointmentId', 'errorMessage']);
        $this->emit('appointmentCompleted');
    } else {
        $this->emit('appointmentError');
    }
}



}