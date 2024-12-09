<?php

namespace App\Http\Livewire;

use App\Models\Appointment;
use App\Models\Employee;
use App\Models\Service;
use App\Models\TimeSlot;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class AddingServiceToCart extends Component
{
    public $service;
    public $selectedEmployee;
    public $selectedTime;
    public $selectedDate;
    public $employees;

    public function mount(Service $service)
{
    $this->service = $service;

    // Fetch only employees assigned to the current service (i.e., those who are related to this service)
    $this->employees = $this->service->employees;  // Assuming you have a relationship set up

    // If there are no employees assigned, handle the case gracefully (perhaps show an error or a default message)
    if ($this->employees->isEmpty()) {
        session()->flash('error', 'No employees are assigned to this service.');
        return redirect()->route('services'); // Redirect to home or an appropriate page
    }

    // Set the selected employee to the first assigned employee (or the only assigned employee)
    $this->selectedEmployee = $this->employees->first()->id;

    // Set all employees as available (assuming availability is based on time and date, handled later)
    $this->employees->map(function ($employee) {
        $employee->available = true; // Mark as available initially
    });
}


    public function render()
    {
        return view('livewire.adding-service-to-cart');
    }

    // When date or time slot is selected, check availability for employees
    public function updatedSelectedDate($selectedDate)
    {
        // Refresh employee availability after date is selected
        $this->displayUnavailableEmployees();
    }

    public function updatedSelectedTime($selectedTime)
    {
        // Refresh employee availability after time slot is selected
        $this->displayUnavailableEmployees();
    }

    // This method will check employee availability based on selected date and time slot
    private function displayUnavailableEmployees()
{
    if (!$this->selectedDate) {
        return;
    }

    $selectedDayOfWeek = Carbon::parse($this->selectedDate)->format('l'); // Get the day name

    // Fetch unavailable employees due to existing appointments
    $unavailableDueToAppointments = Appointment::where('date', $this->selectedDate)
        ->where('time', $this->selectedTime)
        ->pluck('employee_id')
        ->toArray();

    foreach ($this->employees as $employee) {
        // Check if the employee works on the selected day
        $isWorkingDay = in_array($selectedDayOfWeek, $employee->working_days ?? []);

        // Check if the employee is unavailable due to an appointment
        $isUnavailableByAppointment = in_array($employee->id, $unavailableDueToAppointments);

        // Determine availability: the employee must be working and not booked
        $isAvailable = $isWorkingDay && !$isUnavailableByAppointment;

        $employee->available = $isAvailable;

        // Reset selected employee if they become unavailable
        if ($this->selectedEmployee == $employee->id && !$isAvailable) {
            $this->selectedEmployee = null;
        }
    }
}




    // Add the service to the cart
    public function addToCart()
{
    Log::info('Attempting to add to cart', [
        'service_id' => $this->service->id,
        'selected_date' => $this->selectedDate,
        'selected_time' => $this->selectedTime,
        'selected_employee' => $this->selectedEmployee,
    ]);

    try {
        // Check if the service is hidden
        if ($this->service->is_hidden) {
            throw new \Exception('Service is hidden.');
        }

        // Ensure the user is logged in
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Validate employee assignment
        $employee = Employee::find($this->selectedEmployee);
        if (!$employee || !$this->service->employees->contains($employee)) {
            throw new \Exception('Invalid employee assignment.');
        }

        // Get or create cart
        $cart = auth()->user()->cart()->where('is_paid', false)->firstOrCreate([]);

        // Check for duplicate
        if ($cart->services()
            ->where('date', $this->selectedDate)
            ->where('time', $this->selectedTime)
            ->where('employee_id', $this->selectedEmployee)
            ->exists()) {
            throw new \Exception('Duplicate cart item detected.');
        }

        // Add service to cart
        $cart->services()->attach($this->service->id, [
            'time' => $this->selectedTime,
            'date' => $this->selectedDate,
            'employee_id' => $this->selectedEmployee,
            'first_name' => $employee->first_name,
            'price' => $this->service->price,
        ]);

        // Update cart total
        $cart->total = $cart->services()->sum(DB::raw('cart_service.price'));
        $cart->save();

        Log::info('Service added to cart successfully', ['cart_id' => $cart->id]);

        // Trigger SweetAlert and redirect
        $this->dispatchBrowserEvent('swal:alert', [
            'title' => 'Success!',
            'text' => 'Service added to the cart successfully!',
            'icon' => 'success',
            'redirect_url' => route('cart'), // Pass the redirection URL
        ]);

    } catch (\Exception $e) {
        Log::error('Error adding to cart', ['error' => $e->getMessage()]);

        $this->dispatchBrowserEvent('swal:alert', [
            'title' => 'Error!',
            'text' => $e->getMessage(),
            'icon' => 'error',
        ]);
    }
}
}
