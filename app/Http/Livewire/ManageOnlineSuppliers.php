<?php

namespace App\Http\Livewire;

use App\Models\OnlineSupplier;
use Livewire\Component;

class ManageOnlineSuppliers extends Component
{
    private $online_suppliers;

    public $search;
    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public $online_supplier;

    public $confirmingSupplierAdd;
    public $confirmSupplierDeletion = false;
    public $confirmingSupplierDeletion = false;

    protected $rules = [
        "online_supplier.name" => "required|string|max:255",
        "online_supplier.link" => "nullable|string|max:255",
        "online_supplier.contact" => "required|string|max:255",
        "online_supplier.address" => "required|string|max:255",
    ];

    public function render()
    {
        $this->online_suppliers = OnlineSupplier::when($this->search, function($query) {
            $query->where('name','like','%'.$this->search.'%');
        })->paginate(10);

        return view('livewire.manage-online-suppliers',[
            'online_suppliers' => $this->online_suppliers,
        ]);
    }

    public function confirmSupplierEdit(OnlineSupplier $online_supplierId)
    {
        $this->online_supplier = $online_supplierId; // Load existing supplier
        $this->confirmingSupplierAdd = true;
    }

    public function confirmSupplierDeletion()
    {
        $this->confirmingSupplierDeletion = true;
    }

   public function saveOnlineSupplier()
    {
        $this->validate();

        if ($this->online_supplier->id) {
            // Update the existing supplier
            $this->online_supplier->save();
            $this->emit('supplierUpdated'); // Emit update event
        } else {
            // Create a new supplier
            OnlineSupplier::create([
                'name' => $this->online_supplier->name,
                'link' => $this->online_supplier->link,
                'contact' => $this->online_supplier->contact,
                'address' => $this->online_supplier->address,
            ]);
            
            $this->newSupplies['branch_id'] = $this->userBranchId;

            $this->emit('supplierAdded'); // Emit add event
        }

        $this->confirmingSupplierAdd = false;
        $this->online_supplier = null;
    }

    public function deleteSupplier(OnlineSupplier $online_supplierId)
    {
        $this->online_supplier = $online_supplierId;
        $this->online_supplier->delete();
        $this->confirmingSupplierDeletion = false;
    }

    public function confirmSupplierAdd()
    {
        $this->online_supplier = new OnlineSupplier(); // New empty supplier
        $this->confirmingSupplierAdd = true;
    }
}
