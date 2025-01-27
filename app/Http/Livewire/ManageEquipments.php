<?php

namespace App\Http\Livewire;

use App\Models\Category;
use App\Models\Employee;
use App\Models\Equipment;
use App\Models\User;
use App\Notifications\EquipmentNotification;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ManageEquipments extends Component
{
    use WithFileUploads;
    use WithPagination;

    public $equipment;

    public $search;
    public $confirmingEquipmentDeletion = false;
    public $confirmingEquipmentView = false;
    public $showAddEquipmentModal = false;
    public $showEditEquipmentModal = false;
    protected $notifiedEquipments = [];
    public $selectedEquipmentId;
    public $newEquipment = [];
    public $image;
    public $categories;
    public $categoryFilter = null;
    public $statusFilter = 'active';
    public $employees;
    public $paginate = 10;

    protected $listeners = [
        'confirmArchiveEquipment' => 'confirmArchiveEquipment',
        'confirmUnarchiveEquipment' => 'confirmUnarchiveEquipment',
    ];

    public function archiveEquipment($equipmentId)
    {
        $this->emit('confirmArchive',$equipmentId);
    }

    public function confirmArchiveEquipment($equipmentId)
    {
        $equipment = Equipment::findOrFail($equipmentId);
        $equipment->status = 0;
        $equipment->save();

        $this->emit('equipmentArchied');
    }
    public function unarchiveEquipment($equipmentId)
    {
       $this->emit('confirmUnarchive', $equipmentId);
    }

    public function confirmUnarchiveEquipment($equipmentId)
    {
        $equipment = Equipment::findOrFail($equipmentId);
        $equipment->status = 1;
        $equipment->save();

        $this->emit('equipmentUnarchived');
    }

    public function resetFilters()
    {
        $this->categoryFilter = null;
        $this->search = '';
        $this->resetPage(); // Reset pagination to the first page
    }

    public function mount()
    {
        $this->categories = Category::all();
        $this->employees = Employee::all();

        $this->resetNewEquipment();
    }

    public function resetNewEquipment()
    {
        $this->newEquipment = [
            'name' => '',
            'category_id' => '',
            'quantity' => '',
            'last_maintenance' => '',
            'next_maintenance' => '',
            'purchased_date' => '',
            'employee_id' => '',
            'brand_name' => '',
        ];
    }

    public function showAddEquipmentModal()
    {
        $this->resetNewEquipment(); // Clear form fields
        $this->showAddEquipmentModal = true;
    }


    public function showEditEquipmentModal($equipmentId)
    {
        $this->newEquipment = Equipment::findOrFail($equipmentId)->toArray();
        $this->selectedEquipmentId = $equipmentId; // Track editing
        $this->showEditEquipmentModal = true;
    }

    public function closeModals()
    {
        $this->showAddEquipmentModal = false;
        $this->showEditEquipmentModal = false;
        $this->resetNewEquipment(); // Reset data
    }



    public function confirmEquipmentDeletion($equipmentId)
    {
        $this->confirmingEquipmentDeletion = $equipmentId;
    }

    public function deleteEquipment($equipmentId)
    {
        Equipment::find($equipmentId)->delete();
        $this->confirmingEquipmentDeletion = false;
        session()->flash('message', 'Equipment Deleted Successfully');
    }

    public function saveEquipment()
    {
        $this->validate([
            'newEquipment.name' => 'required|string|max:255',
            'newEquipment.brand_name' => 'required|string|max:255',
            'newEquipment.category_id' => 'required|exists:categories,id',
            'newEquipment.employee_id' => 'required|integer|exists:employees,id',
            'newEquipment.quantity' => 'required|integer|min:0',
            'newEquipment.last_maintenance' => 'nullable|date',
            'newEquipment.next_maintenance' => 'nullable|date|after:last_maintenance',
            'newEquipment.purchased_date' => 'required|date',
            'image' => 'nullable|image|max:2048',
        ]);

        // Convert empty strings to NULL for nullable date fields
        $this->newEquipment['last_maintenance'] = $this->newEquipment['last_maintenance'] ?: null;
        $this->newEquipment['next_maintenance'] = $this->newEquipment['next_maintenance'] ?: null;

        if ($this->image) {
            $path = $this->image->store('images', 'public');
            $this->newEquipment['image'] = $path;
        }

        Equipment::updateOrCreate(
            ['id' => $this->selectedEquipmentId],
            $this->newEquipment
        );

        session()->flash('message', $this->selectedEquipmentId ? 'Equipment updated successfully!' : 'Equipment added successfully!');

        $this->closeModals();

        $this->dispatchBrowserEvent('equipmentAddedOrUpdated');
    }



    public function viewEquipment($equipmentId)
    {
        $this->equipment = Equipment::find($equipmentId);

        if(!$this->equipment){
            $this->dispatchBrowserEvent('notification',['message' => 'Equipment not found']);
        }

        $this->confirmingEquipmentView = true;
    }

    public function render()
    {
            $query = Equipment::with(['employee', 'category'])
        ->when($this->categoryFilter, function ($query) {
            $query->where('category_id', $this->categoryFilter);
        })
        ->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('brand_name', 'like', '%' . $this->search . '%');
            });
        })
        ->when($this->statusFilter == 'active', function ($query) {
            $query->where('status', 1);
        })
        ->when($this->statusFilter == 'archived', function ($query) {
            $query->where('status', 0);
        });

    $equipments = $query->paginate($this->paginate);

    foreach ($equipments as $equipment) {
        $maintenanceDate = Carbon::parse($equipment->next_maintenance);
        $lowQuantity = $equipment->quantity < 10;
    
        if ($lowQuantity && !in_array($equipment->id, $this->notifiedEquipments)) {
            $this->notifyAdminAndEmployees($equipment, 'low_quantity');
            $this->notifiedEquipments[] = $equipment->id;
        }
    
        if (($maintenanceDate->isToday() || $maintenanceDate->isTomorrow()) && !in_array($equipment->id . 'maintenance', $this->notifiedEquipments)) {
            $this->notifyAdminAndEmployees($equipment, 'maintenance_due');
            $this->notifiedEquipments[] = $equipment->id . 'maintenance';
        }
    }

        return view('livewire.manage-equipments', [
            'equipments' => $equipments,
            'categories' => $this->categories,
        ]);
    }

    protected function notifyAdminAndEmployees($equipment, $type)
    {
        // Get the admin and all employees
        $admin = User::where('role_id', 1)->first(); // Assuming there's a role column
        $employees = User::where('role_id', 2)->get(); // Assuming employees have the role 'employee'
    
        // Notify the admin
        if ($admin) {
            $admin->notify(new EquipmentNotification($equipment, $type));
        }
    
        // Notify all employees
        foreach ($employees as $employee) {
            $employee->notify(new EquipmentNotification($equipment, $type));
        }
    }

public function exportToPdf()
{
    $equipments = Equipment::with(['employee', 'category'])
        ->when($this->categoryFilter, function ($query) {
            $query->where('category_id', $this->categoryFilter);
        })
        ->when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('brand_name', 'like', '%' . $this->search . '%');
            });
        })
        ->get();

    if ($equipments->isEmpty()) {
        session()->flash('message', 'No equipment found for export.');
        return;
    }

    $image = public_path('images/banner-purple.png');
    $preparedBy = auth()->user()->name ?? 'System Admin';
    $currentDateTime = now()->format('Y-m-d H:i:s');

    $pdf = Pdf::loadView('equipment-report', compact('equipments', 'image', 'preparedBy', 'currentDateTime'));

    $pdfPath = 'pdf/equipment-report-' . time() . '.pdf';
    Storage::disk('public')->put($pdfPath, $pdf->output());

    // Dispatch the download event
    $this->dispatchBrowserEvent('downloadFile', ['url' => Storage::url($pdfPath)]);
}
}
