<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Service;
use App\Models\Branch;
use App\Models\Employee;
use App\Models\User;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class ManageServices extends Component
{
    use WithPagination;
    use WithFileUploads;


    public $showAddServiceModal = false; // Modal for adding services

    public $showEditServiceModal = false; // Modal for editing services
     // For Edit Modal
    public $selectedEmployeeId = null;
    public $selectedBranchId = null; 
    public $image;

    public $search;
    public $statusFilter = 'active';
    public $categoryFilter = ''; // Default: show all categories

    public $employeeIds = [];
    public $branchIds = [];


    
    
    public $newService; // Declare $newService explicitly
    public $selectedServiceId; // Declare $selectedServiceId explicitly

    public function mount()
    {
        // Initialize values if needed
        $this->newService = ''; 
        $this->selectedServiceId = null;
    }
    
    public function showAddServiceModal()
    {
        $this->resetNewService(); // Clear form fields
        $this->showAddServiceModal = true;
    }

    public function showEditServiceModal($serviceId)
    {
        $this->newService = Service::findOrFail($serviceId)->toArray();
        $this->selectedServiceId = $serviceId; // Track editing
        $this->showEditServiceModal = true;
    }

        public function closeModals()
    {
        $this->resetNewService();
        $this->showAddServiceModal = false;
        $this->showEditServiceModal = false;
    }

    protected $listeners = [
        'confirmArchiveService' => 'confirmArchiveService',
        'confirmUnarchiveService' => 'confirmUnarchiveService',
    ];

    public function archiveService($serviceId)
    {
        $this->emit('confirmArchive', $serviceId);
    }

    public function confirmArchiveService($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $service->is_hidden = 1; // Archived
        $service->save();

        $this->emit('serviceUnarchived'); // Notify front-end for success
    }

    public function unarchiveService($serviceId)
    {
        $this->emit('confirmUnarchive', $serviceId);
    }

    public function confirmUnarchiveService($serviceId)
    {
        $service = Service::findOrFail($serviceId);
        $service->is_hidden = 0; // Active
        $service->save();

        $this->emit('serviceArchived'); // Notify front-end for success
    }


    public function resetNewService()
        {
            $this->newService = [
                'name' => '',
                'slug' => '',
                'description' => '',
                'price' => '',
                'category_id' => '',
                'allergens' => '',
            ];
            $this->image = null;
            $this->selectedServiceId = null;
            $this->employeeIds = [];
            $this->selectedBranchId = null;
            $this->branchIds = [];
        }




    public function saveService()
{
    try {
        // Validation
        $this->validate([
            'newService.name' => 'required|string|min:1|max:255',
            'newService.slug' => 'unique:services,slug,' . ($this->selectedServiceId ?? 'NULL') . ',id',
            'newService.description' => 'required|string|min:1|max:255',
            'newService.price' => 'required|numeric|min:0',
            'newService.category_id' => 'required|exists:categories,id',
            'newService.allergens' => 'nullable|string|max:255',
        ]);

        // Handle Image Upload
        if ($this->image) {
            $this->newService['image'] = $this->image->store('images', 'public');
        }

        // Update or Create Service
        if ($this->selectedServiceId) {
            // Update existing service
            $service = Service::findOrFail($this->selectedServiceId);
            $service->update($this->newService);
        } else {
            // Create new service
            $this->newService['slug'] = \Str::slug($this->newService['name']);
            $service = Service::create($this->newService);
        }

        // Sync employees if applicable
        if (isset($this->employeeIds)) {
            $service->employees()->sync($this->employeeIds);
        }
        
        // Sync branches if applicable
        if (isset($this->branchIds)) {
            $service->branches()->sync($this->branchIds);
        }

        // Reset Form and Emit Success
        session()->flash('message', $this->selectedServiceId ? 'Service updated successfully!' : 'Service added successfully!');
        $this->resetNewService();
        $this->showAddServiceModal = false;
        $this->showEditServiceModal = false;

        $this->dispatchBrowserEvent('serviceAddedOrUpdated');
    } catch (\Illuminate\Database\QueryException $e) {
        // Handle database errors
        if ($e->errorInfo[1] == 1062) {
            session()->flash('error', 'The service slug is already taken. Please choose a different name.');
        } else {
            session()->flash('error', 'An error occurred while saving the service.');
        }
    } catch (\Exception $e) {
        // Handle general exceptions
        session()->flash('error', 'An unexpected error occurred: ' . $e->getMessage());
    }
}

public function render()
{
    $user = auth()->user();
    $branches = Branch::all();
    $categories = \App\Models\Category::all();

    // Default branch selection for non-admin users
    if ($user->role_id !== 1) {
        $this->selectedBranchId = $user->branch_id; 
    }

    $services = Service::when($this->statusFilter == 'active', function ($query) {
            $query->where('is_hidden', 0); // Active services
        })
        ->when($this->statusFilter == 'archived', function ($query) {
            $query->where('is_hidden', 1); // Archived services
        })
        ->when($this->categoryFilter, function ($query) {
            $query->where('category_id', $this->categoryFilter); // Category filter
        })
        ->when($this->search, function ($query) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%')
                ->orWhereHas('category', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                });
        })
        ->when($user->role_id !== 1, function ($query) use ($user) {
            $query->whereHas('branches', function ($branchQuery) use ($user) {
                $branchQuery->where('branch_id', $user->branch_id);
            });
        })
        ->orderBy('created_at', 'desc') 
        ->paginate(10);

        $employees = Employee::when($user->role_id !== 1, function ($query) use ($user) {
            $query->where('branch_id', $user->branch_id);
        })->get();
        

    // Fetch employees only from the logged-in user's branch
  

    return view('livewire.manage-services', compact('services', 'categories', 'employees', 'branches'));
}








}
