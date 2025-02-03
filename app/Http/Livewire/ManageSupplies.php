<?php

    namespace App\Http\Livewire;

    use App\Models\Category;
    use App\Models\OnlineSupplier;
    use App\Models\Supply;
    use App\Models\Branch;
    use App\Models\User;
    use App\Models\Employee;
    use App\Notifications\ConsumablesNotification;
    use Livewire\Component;
    use Livewire\WithFileUploads;
    use Livewire\WithPagination;
    use Barryvdh\DomPDF\Facade\Pdf;
    use Carbon\Carbon;
    use Storage;

    class ManageSupplies extends Component
    {
        use WithFileUploads;
        use WithPagination;

        public $supply;
        public $search;
        public $confirmingSuppliesDeletion = false;
        public $confirmingSuppliesView = false;
        public $showAddSuppliesModal = false;
        public $showEditSuppliesModal = false;
        public $selectedSupplyId;
        public $newSupplies = [];
        public $image;
        public $categories;
        public $branches;
        public $branchFilter = '';
        public $online_suppliers;
        public $categoryFilter = '';
        public $selectFilter = 'all';  // Default to 'all' filter
        public $statusFilter = 'active';
        protected $notifiedSupplies = [];
        public $paginate = 10;

        protected $listeners = [
            'confirmArchiveSupplies' => 'confirmArchiveSupplies',
            'confirmUnarchiveSupplies' => 'confirmUnarchiveSupplies',
        ];

        public function archiveSupplies($supplyId)
        {
            $this->emit('confirmArchive',$supplyId);
        }

        public function confirmArchiveSupplies($supplyId)
        {
            $supply = Supply::findOrFail($supplyId);
            $supply->status = 0;
            $supply->save();

            $this->emit('suppliesArchied');
        }
        public function unarchiveSupplies($supplyId)
        {
           $this->emit('confirmUnarchive', $supplyId);
        }

        public function confirmUnarchiveSupplies($supplyId)
        {
            $supply = Supply::findOrFail($supplyId);
            $supply->status = 1;
            $supply->save();

            $this->emit('suppliesUnarchived');
        }


        public function resetFilters()
        {
            $this->categoryFilter = null;
            $this->search = '';
            $this->resetPage(); // Reset pagination to the first page
        }

        public function mount()
        {
            // Fetch categories and online suppliers
            $this->categories = Category::all();
            $this->branches = Branch::all();
            $this->online_suppliers = OnlineSupplier::all();

            $this->resetNewSupplies();
        }

        public function resetNewSupplies()
        {
            $this->newSupplies = [
                'name' => '',
                'description' => '',
                'quantity' => '',
                'category_id' => '',
                'color_code' => '',
                'color_shade' => '',
                'size' => '',
                'expiration_date' => '',
                'online_supplier_id' => '',
            ];
        }

        public function showAddSuppliesModal()
        {
            $this->resetNewSupplies(); // Clear form fields
            $this->confirmingSuppliesView = false;  // Close view modal
            $this->showAddSuppliesModal = true;
        }

        public function showEditSuppliesModal($supplyId)
        {
            $supply = Supply::findOrFail($supplyId);
            $this->newSupplies = [
                'name' => $supply->name,
                'description' => $supply->description,
                'quantity' => $supply->quantity,
                'category_id' => $supply->category_id,
                'color_code' => $supply->color_code,
                'color_shade' => $supply->color_shade,
                'size' => $supply->size,
                'expiration_date' => $supply->expiration_date,
                'online_supplier_id' => $supply->online_supplier_id,
                'branch_id' => $supply->branch_id,
            ];
            
            $this->selectedSupplyId = $supplyId;
            $this->confirmingSuppliesView = false;  
            $this->showEditSuppliesModal = true;
            
        }

        public function viewSupplies($supplyId)
        {
            // Close other modals first
            $this->showAddSuppliesModal = false;
            $this->showEditSuppliesModal = false;

            $this->supply = Supply::find($supplyId);

            if (!$this->supply) {
                $this->dispatchBrowserEvent('notification', ['message' => 'Supply not found']);
            }

            // Show the view modal
            $this->confirmingSuppliesView = true;
        }

        public function closeModals()
        {
            // Close all modals when the close button is clicked
            $this->showAddSuppliesModal = false;
            $this->showEditSuppliesModal = false;
            $this->confirmingSuppliesView = false;
            $this->resetNewSupplies(); // Reset data when closing modals
        }

        public function confirmSuppliesDeletion($supplyId)
        {
            $this->confirmingSuppliesDeletion = $supplyId;
        }

        public function deleteSupplies($supplyId)
        {
            Supply::find($supplyId)->delete();
            $this->confirmingSuppliesDeletion = false;
            session()->flash('message', 'Supply Deleted Successfully.');
        }

        public function saveSupplies()
        {
        $this->validate([
            'newSupplies.name' => 'required|string|max:255',
            'newSupplies.description' => 'required|string|max:255',
            'newSupplies.quantity' => 'required|integer|min:0',
            'newSupplies.category_id' => 'required|exists:categories,id',
            'newSupplies.online_supplier_id' => 'nullable|exists:online_suppliers,id',
            'newSupplies.color_code' =>  'nullable|string|unique:supplies,color_code,' . $this->selectedSupplyId,
            'newSupplies.color_shade' => 'nullable|string|max:255',
            'newSupplies.size' => 'nullable|string|max:255',
            'newSupplies.expiration_date' => 'nullable|date',
            'image' => 'nullable|image|max:2048',
        ]);

        $user = auth()->user();
        if ($user->role_id == 1) { // Admin
            $this->validate([
                'newSupplies.branch_id' => 'required|exists:branches,id',
            ]);
        } else { // Employee
            $this->newSupplies['branch_id'] = $user->branch_id;
        }

        // Handle image upload
        if ($this->image) {
            $path = $this->image->store('images', 'public');
            $this->newSupplies['image'] = $path;
        }

        // Save or update the supply record
        Supply::updateOrCreate(
            ['id' => $this->selectedSupplyId ?: null], // Ensure update happens when ID exists
            [
                'name' => $this->newSupplies['name'],
                'description' => $this->newSupplies['description'],
                'quantity' => $this->newSupplies['quantity'],
                'category_id' => $this->newSupplies['category_id'],
                'color_code' => $this->newSupplies['color_code'],
                'color_shade' => $this->newSupplies['color_shade'],
                'size' => $this->newSupplies['size'],
                'expiration_date' => $this->newSupplies['expiration_date'],
                'online_supplier_id' => $this->newSupplies['online_supplier_id'],
                'branch_id' => $this->newSupplies['branch_id'],
            ]
        );
        
        session()->flash('message', $this->selectedSupplyId ? 'Supply Updated Successfully!' : 'Supply Added Successfully');
        $this->closeModals();

        $this->dispatchBrowserEvent('suppliesAddedOrUpdated');

        }

        public function render()
        {

            $user = auth()->user();

            $query = Supply::with(['category', 'online_supplier'])
            ->when($user->role_id !=1, function ($query) use ($user) {
                $query->where('branch_id', $user->branch_id);
            })
                ->when($this->categoryFilter, function ($query) {
                    $query->where('category_id', $this->categoryFilter);
                })
                ->when($this->branchFilter, function ($query) {
                    $query->where('branch_id', $this->branchFilter);
                })
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('description', 'like', '%' . $this->search . '%');
                    });
                })
                ->when($this->statusFilter == 'active', function ($query) {
                    $query->where('status', 1);
                })
                ->when($this->statusFilter == 'archived', function ($query) {
                    $query->where('status', 0);
                });

            // Apply filters based on the selected filter
            if ($this->selectFilter === 'expired') {
                $query->whereDate('expiration_date', '<', now());
            } elseif ($this->selectFilter === 'low_quantity') {
                $query->where('quantity', '<', 10);  // Low quantity threshold, adjust as needed
            }

            // Get paginated supplies
            $supplies = $query->paginate($this->paginate ?: 10);

            foreach ($supplies as $supply) {
                $nearExpiration = Carbon::parse($supply->expiration_date);
                $lowQuantity = $supply->quantity < 10;
            
                // Notify for low quantity
                if ($lowQuantity && !in_array($supply->id, $this->notifiedSupplies)) {
                    $this->notifyAdminAndEmployees($supply, 'low_quantity');
                    $this->notifiedSupplies[] = $supply->id;
                }
            
                // Notify for near expiration (1 week before the expiration date)
                if (
                    $nearExpiration->diffInDays(Carbon::today()) <= 7 &&
                    !$nearExpiration->isPast() &&
                    !in_array($supply->id . 'near_expiration', $this->notifiedSupplies)
                ) {
                    $this->notifyAdminAndEmployees($supply, 'expiration_date');
                    $this->notifiedSupplies[] = $supply->id . 'expiration_date';
                }
            }

            return view('livewire.manage-supplies', [
                'supplies' => $supplies,
                'categories' => $this->categories,
                'branches' => $this->branches,
                'online_suppliers' => $this->online_suppliers,
            ]);
        }

        public function notifyAdminAndEmployees($supply, $type)
        {
            $admin = User::where('role_id', 1)->first();
            $employees = User::where('role_id', 2)->get();

            if ($admin) {
                $admin->notify(new ConsumablesNotification($supply, $type));
            }

            foreach ($employees as $employee) {
                $employee->notify(new ConsumablesNotification($supply, $type));
            }

        }

        public function exportToPdf()
        {
            $supplies = Supply::with(['category'])
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

            // Check if supplies data is empty
            if ($supplies->isEmpty()) {
                session()->flash('message', 'No supplies found for export.');
                return;
            }

            $image = public_path('images/banner-purple.png'); // Adjust the path to your image file
            $preparedBy = auth()->user()->name ?? 'System Admin';
            $currentDateTime = now()->format('Y-m-d H:i:s');

            $pdf = Pdf::loadView('supplies-report', [
                'supplies' => $supplies,
                'preparedBy' => $preparedBy,
                'currentDateTime' => $currentDateTime,
                'image' => $image, // Pass the image path to the view
            ]);

            $pdfPath = 'pdf/supplies-report-' . time() . '.pdf';
            Storage::disk('public')->put($pdfPath, $pdf->output());

            $this->dispatchBrowserEvent('downloadFile', ['url' => Storage::url($pdfPath)]);
        }
    }
