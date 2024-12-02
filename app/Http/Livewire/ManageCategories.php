<?php

namespace App\Http\Livewire;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithFileUploads;

class ManageCategories extends Component
{
    use WithFileUploads;

    private $categories;
    public $image;
    public $search;

    protected $queryString = [
        'search' => ['except' => ''],
    ];
    public $category;

    public $confirmingCategoryAdd;

    protected $rules = [
        "category.name" => "required|string|max:255",
    ];
    public function render()
    {
        $this->categories = Category::when($this->search, function ($query) {
            $query->where('name', 'like', '%'.$this->search.'%');
        })->paginate(10);

        return view('livewire.manage-categories', [
            'categories' => $this->categories,
        ]);
    }

    public function confirmCategoryEdit(Category $category) {
        $this->category = $category;
        $this->confirmingCategoryAdd= true;
    }

    public function saveCategory()
    {
        $this->validate([
            'category.name' => 'required|string|max:255',
            'image' => 'nullable|image|max:2048', // Validate the image
        ]);

        $imagePath = null;

        if ($this->image) {
            $imagePath = $this->image->store('categories', 'public'); // Save image to 'storage/app/public/categories'
        }

        if (isset($this->category->id)) {
            $this->category->update([
                'name' => $this->category['name'],
                'image' => $imagePath ?: $this->category->image,
            ]);
        } else {
            Category::create([
                'name' => $this->category['name'],
                'image' => $imagePath,
            ]);
        }

        $this->confirmingCategoryAdd = false;
        $this->category = null;
        $this->image = null; // Reset the image input
    }



    public function confirmCategoryAdd() {
        $this->confirmingCategoryAdd = true;
    }

}
