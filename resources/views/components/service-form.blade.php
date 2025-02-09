<div>
    <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
    <input type="text" wire:model="newService.name" id="name" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
    @error('newService.name') <span class="text-red-500">{{ $message }}</span>@enderror
</div>


<div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-3">
<div>
    <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
    <input type="text" wire:model="newService.price" id="price" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

    @error('newService.price') <span class="text-red-500">{{ $message }}</span>@enderror

</div>

<div>
    <label for="category_id" class="block text-sm font-medium text-gray-700">Category</label>

    <select wire:model="newService.category_id" id="category_id" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
        <option disabled selected value="">Select Category</option>
        @foreach ($categories as $category)
            <option value="{{ $category->id }}">{{ $category->name}}</option>
        @endforeach
        @error('newService.category_id') <span class="text-red-500">{{ $message }}</span>@enderror
    </select>
</div>
</div>




<div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-2">
    <div>
        <label for="allergens" class="block text-sm font-medium text-gray-700">Allergens</label>
        <textarea id="allergens" wire:model="newService.allergens"  class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
        @error('newService.allergens') <span class="text-red-500">{{ $message }}</span>@enderror
        </div>
<div>
    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
    <textarea id="description" wire:model="newService.description"  class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
    @error('newService.description') <span class="text-red-500">{{ $message }}</span>@enderror
</div>

<div class="form-group">
    <label for="employees">Assign Employees</label>
    <div class="grid grid-cols-2 gap-4">
        @foreach ($employees as $employee)
            <div class="flex items-center">
                <input type="checkbox"
                       wire:model="employeeIds"
                       value="{{ $employee->id }}"
                       id="employee_{{ $employee->id }}"
                       class="form-checkbox h-5 w-5 text-blue-600">
                <label for="employee_{{ $employee->id }}" class="ml-2 text-sm">{{ $employee->first_name }} - {{ $employee->jobCategory?->name }}</label>
            </div>
        @endforeach
    </div>
    @error('employeeIds')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
<div class="form-group">
    <label for="branches">Assign Branches</label>
    <div class="grid grid-cols-2 gap-4">
        @foreach ($branches as $branch)
            <div class="flex items-center">
                <input type="checkbox"
                       wire:model="branchIds"
                       value="{{ $branch->id }}"
                       id="branch_{{ $branch->id }}"
                       class="form-checkbox h-5 w-5 text-blue-600">
                <label for="branch_{{ $branch->id }}" class="ml-2 text-sm">{{ $branch->name }}</label>
            </div>
        @endforeach
    </div>
    @error('branchIds')
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
</div>


<div class="grid grid-cols-1 gap-6 mt-4 sm:grid-cols-2">
<div class="col-span-2">
    <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
    <input type="file" wire:model.defer="image" id="image" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
    @error('image') <span class="text-red-500">{{ $message }}</span>@enderror

    {{-- If the image is already saved is system show img --}}
     @if (isset($image) && is_string($image))
        <img alt="image" src="{{ '/storage/' . $image }}" class="mt-4" width="200">
    {{-- When the image is uploaded show img --}}
     @elseif (isset($image) && is_object($image))
        <img alt="image" src="{{ $image->temporaryUrl() }}" class="mt-4" width="200">
     @else

    @endif

</div>
</div>
