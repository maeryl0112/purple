<?php

namespace App\Http\Livewire;

use App\Models\Employee;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class ManageEmployees extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $employee;
    public $search;
    public $confirmingEmployeeView = false;
    public $showAddEmployeeModal = false; // For Add Modal
    public $showEditEmployeeModal = false; // For Edit Modal
    public $selectedEmployeeId = null; // Tracks the employee being edited
    public $workingDays = [];
    public $jobCategoryFilter = '';
    public $statusFilter = 'active'; // For filtering active and archived employees
    public $allDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    public $newEmployee = [];
    public $image;

    public $birthday;
    public $age;

    public function updatedNewEmployeeBirthday($value)
    {
        $this->newEmployee['age'] = $this->calculateAge($value); // Store age in newEmployee
    }

    public function calculateAge($birthday)
    {
        return Carbon::parse($birthday)->age;
    }


    public function showAddEmployeeModal()
    {
        $this->resetNewEmployee(); // Clear form fields
        $this->showAddEmployeeModal = true;
    }

    public function showEditEmployeeModal($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $this->newEmployee = $employee->toArray();
        $this->workingDays = $employee->working_days ?? [];
        $this->selectedEmployeeId = $employee->id;
        $this->age = Carbon::parse($this->newEmployee['birthday'])->age;
        $this->newEmployee['job_category_id'] = $employee->job_category_id;
        $this->showEditEmployeeModal = true;
    }

    protected $listeners = [
        'confirmArchiveEmployee' => 'confirmArchiveEmployee',
        'confirmUnarchiveEmployee' => 'confirmUnarchiveEmployee',
    ];

    public function archiveEmployee($employeeId)
    {
        $this->emit('confirmArchive',$employeeId);
    }

    public function confirmArchiveEmployee($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $employee->status = 0;
        $employee->save();

        $this->emit('employeeArchied');
    }
    public function unarchiveEmployee($employeeId)
    {
       $this->emit('confirmUnarchive', $employeeId);
    }

    public function confirmUnarchiveEmployee($employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $employee->status = 1;
        $employee->save();

        $this->emit('employeeUnarchived');
    }

    public function resetNewEmployee()
    {
        $this->newEmployee = [
            'first_name' => '',
            'last_name' => '',
            'age' => null,
            'phone_number' => '',
            'job_category_id' => '',
            'birthday' => null,
            'date_started' => '',
            'address' => '',
            'email' => '',
            'is_hidden' => false,
        ];

        $this->image = null;
        $this->selectedEmployeeId = null;
    }





    public function saveEmployee()
{

    $this->validate([
        'newEmployee.first_name' => 'required|string|max:255',
        'newEmployee.last_name' => 'required|string|max:255',
        'newEmployee.email' => 'required|email|unique:employees,email,' . $this->selectedEmployeeId,
        'newEmployee.phone_number' => 'required|string|max:15',
        'newEmployee.job_category_id' => 'nullable|exists:job_categories,id',
        'newEmployee.birthday' => 'required|date',
        'newEmployee.date_started' => 'required|date',
        'newEmployee.address' => 'nullable|string',
        'newEmployee.is_hidden' => 'boolean',
        'image' => 'nullable|image|max:5120',
        'workingDays' => 'array', // Validate working days
        'workingDays.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
    ]);

    if ($this->image) {
        $path = $this->image->store('images', 'public');
        $this->newEmployee['image'] = $path;
    }

    $this->newEmployee['working_days'] = $this->workingDays; // Add working days to newEmployee

    if ($this->selectedEmployeeId) {
        Employee::findOrFail($this->selectedEmployeeId)->update($this->newEmployee);
    } else {
        Employee::create($this->newEmployee);
    }

    session()->flash('message', $this->selectedEmployeeId ? 'Employee updated successfully!' : 'Employee added successfully!');
    $this->reset('newEmployee', 'image', 'workingDays');
    $this->showAddEmployeeModal = false;
    $this->showEditEmployeeModal = false;

    $this->dispatchBrowserEvent('employeeAddedOrUpdated');
}

    public function viewEmployee($employeeId)
    {
        $this->employee = Employee::find($employeeId);
        if (!$this->employee) {
            $this->dispatchBrowserEvent('notification', ['message' => 'Employee not found']);
        }
        $this->confirmingEmployeeView = true;
    }

    public function render()
    {
        $job_categories = \App\Models\JobCategory::all();

        $employees = Employee::when($this->statusFilter == 'active', function($query) {
                        return $query->where('status', 1);
                    })
                    ->when($this->statusFilter == 'archived', function($query) {
                        return $query->where('status', 0);
                    })
                    ->when($this->jobCategoryFilter, function ($query) {
                        $query->where('job_category_id', $this->jobCategoryFilter); // Filter by category
                    })
                    ->when($this->search, function ($query) {
                        $query->where(function ($query) {
                            $query->where('first_name', 'like', '%' . $this->search . '%')
                                ->orWhere('last_name', 'like', '%' . $this->search . '%')
                                ->orWhere('phone_number', 'like', '%' . $this->search . '%')
                                ->orWhere('email', 'like', '%' . $this->search . '%');
                        });
                    })
                    ->orderBy($this->sortField ?? 'created_at', $this->sortDirection ?? 'desc')
                    ->paginate(10);

        return view('livewire.manage-employees', [
            'employees' => $employees,
            'job_categories' => $job_categories,
        ]);
    }
}
