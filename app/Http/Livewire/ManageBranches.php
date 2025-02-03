<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Branch;

class ManageBranches extends Component
{
    private $branches;
    public $search;
    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public $branch;
    public $confirmingBranchAdd;
    public $confirmBranchDeletion = false;
    public $confirmingBranchDeletion = false;

    protected $rules = [
        'branch.name' => "required|string|max:255",
    ];

    public function render()
    {
        $this->branches = Branch::when($this->search, function
        ($query) {
            $query->where('name','like','%'.$this->search.'%');
        })
        ->orderBy('created_at','desc')
        ->paginate(10);

        return view('livewire.manage-branches', ['branches' => $this->branches,]);
    }

    public function confirmBranchEdit(Branch $branch)
    {
        $this->branch = $branch;
        $this->confirmingBranchAdd = true;
    }

    public function confirmBranchDeletion()
    {
        $this->confirmingBranchDeletion = true;
    }

    public function saveBranch()
    {
        $this->validate();

        if (isset($this->branch->id)) {
            $this->branch->save();
            $this->dispatchBrowserEvent('branch-saved', ['message' => 'Branch updated successfully!']);
        } else {
            Branch::create([
                'name' => $this->branch['name'],
            ]);
            $this->dispatchBrowserEvent('branch-saved', ['message' => 'Branch added successfully!']);
        }

        $this->confirmingBranchAdd = false;
        $this->branch = null;
    }

    public function deleteBranch(Branch $branchId)
    {
        $this->branch = $branchId;
        $this->branch->delete();
        $this->dispatchBrowserEvent('branch-saved', ['message' => 'Branch deleted successfully!']);
        $this->confirmingBranchDeletion = false;
    }

    public function confirmBranchAdd()
    {
        $this->confirmingBranchAdd = true;
    }
}
