<?php

namespace App\Http\Livewire;

use App\Models\JobCategory;
use Livewire\Component;

class ManageJobCategories extends Component
{
    private $job_categories;
    public $search;
    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public $job_category;
    public $confirmingJobCategoryAdd;
    public $confirmJobCategoryDeletion  = false;
    public $confirmingJobCategoryDeletion = false;

    protected $rules = [
        "job_category.name" => "required|string|max:255",
    ];

    public function render()
    {
        $this->job_categories = JobCategory::when($this->search, function ($query) {
            $query->where('name', 'like', '%'.$this->search.'%');
        })
        ->orderBy('created_at', 'desc') 
        ->paginate(10);

        return view('livewire.manage-job-categories', [
            'job_categories' => $this->job_categories,
        ]);
    }

    public function confirmJobCategoryEdit(JobCategory $job_category)
    {
        $this->job_category = $job_category;
        $this->confirmingJobCategoryAdd = true;
    }

    public function confirmJobCategoryDeletion()
    {
        $this->confirmingJobCategoryDeletion = true;
    }

    public function saveJobCategory()
    {
        $this->validate();

        if (isset($this->job_category->id)) {
            $this->job_category->save();
            $this->dispatchBrowserEvent('job-category-saved', ['message' => 'Job category updated successfully!']);
        } else {
            JobCategory::create([
                'name' => $this->job_category['name'],
            ]);
            $this->dispatchBrowserEvent('job-category-saved', ['message' => 'Job category added successfully!']);
        }

        $this->confirmingJobCategoryAdd = false;
        $this->job_category = null;
    }

    public function deleteJobCategory(JobCategory $job_categoryId)
    {
        $this->job_category = $job_categoryId;
        $this->job_category->delete();
        $this->dispatchBrowserEvent('job-category-saved', ['message' => 'Job category deleted successfully!']);
        $this->confirmingJobCategoryDeletion = false;
    }

    public function confirmJobCategoryAdd()
    {
        $this->confirmingJobCategoryAdd = true;
    }
}
