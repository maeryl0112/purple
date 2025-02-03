<div class="p-4 sm:ml-64">
    <div class="flex justify-between mx-7">
    <h2 class="text-2xl font-bold text-salonPurple">MANAGE SERVICES</h2>


</div>


@if (session()->has('message'))
<script>
    window.addEventListener('serviceAddedOrUpdated', event => {
        Swal.fire({
            title: 'Success!',
            text: "{{ session('message') }}",
            icon: 'success',
            confirmButtonText: 'OK'
        });
    });
</script>
@endif


<div class="overflow-auto rounded-lg border border-gray-200 shadow-md m-5">
    <div class="w-full m-4 flex">
    <div class="w-1/2 mx-2">
        <button  wire:click="showAddServiceModal"  type="button" class="focus:outline-none text-white bg-salonPurple hover:bg-darkPurple focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">ADD</button>

        <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only ">Search</label>
        <div class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                </svg>
            </div>
            <input type="search" wire:model="search" id="default-search" name="search" class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="Search Services...">
           
        </div>

        <div class="py-2.5 me-2">
        <select wire:model="statusFilter" class="border text-gray-900 px-5 pt-2.5 me-2 border-gray-300 rounded-lg">
            <option value="active">Active Services</option>
            <option value="archived">Archived Services</option>
        </select>

        <select wire:model="categoryFilter" class="border text-gray-900 px-5 pt-2.5 me-2 border-gray-300 rounded-lg">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>
        
        </div>
    </div>
    </div>

    <table class="w-full border-collapse bg-white text-left text-sm text-gray-500 overflow-x-scroll min-w-screen">
      <thead class="bg-gray-50">
        <tr>
          <th scope="col" class="pl-6 py-4 font-medium text-gray-900">Id</th>
          <th scope="col" class="px-4 py-4 font-medium text-gray-900">Service</th>
          <th scope="col" class="px-8 py-4 font-medium text-gray-900">Photo</th>
          <th scope="col" class="px-6 py-4 font-medium text-gray-900">Description</th>
          <th scope="col" class="px-6 py-4 font-medium text-gray-900">Price</th>
          <th scope="col" class="px-6 py-4 font-medium text-gray-900">Category</th>
          <th scope="col" class="px-6 py-4 font-medium text-gray-900">Status</th>
          <th scope="col" class="px-6 py-4 font-medium text-gray-900">Actions</th>
          <th scope="col" class="px-4 py-2 font-medium text-gray-900"></th>

        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100 border-t border-gray-100">

        @foreach ($services as $service)
        <tr class="hover:bg-gray-50">
            <td class="pl-6 py-4  max-w-0">{{ $service->id }}</td>

            <th class="px-6 py-4  max-w-0">

                <div class="font-medium text-gray-700">{{ $service->name}}</div>

            </th>
            <td class="px-8 py-4  max-w-0">
                <div class="font-medium text-gray-700">
                    @if ($service->image)
                        <img src="{{ asset('storage/' . $service->image) }}" alt="" class="w-20 h-20 object-cover">
                    @else
                        <span>No image available</span>
                    @endif
                </div>
            </td>

            <td class="px-6 py-4 max-w-0">{{ $service->description }}</td>

            <td class="px-6 py-4  max-w-0">
                <div class="font-medium text-gray-700">{{ $service->price}}</div>
            </td>
            <td class="px-6 py-4  max-w-0">
{{--                    @dd($service->category->name)--}}
                <div class="font-medium text-gray-700">{{ $service->category?->name}}</div>
            </td>


            <td class="px-6 py-4">
                <span class="{{ $service->is_hidden ?  'bg-red-50 text-red-600' : 'bg-green-50 text-green-600'  }} px-2 py-1 text-xs font-medium rounded-full">
                    {{ $service->is_hidden ?   'Archived' : 'Active'}}
            </td>


            </td>
            <td class="px-6 py-4  max-w-0">
                <div class="mt-5 ">
                    <a href="{{ route('view-service', ['slug' => $service->slug ])  }}" >
                        <button class="text-white bg-gradient-to-r from-purple-500 via-purple-600 to-purple-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-purple-300 dark:focus:ring-purple-800 w-24 rounded-lg text-xs px-6 py-2 inline-flex items-center me-1 mb-2">
                            <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                              </svg>

                            View
                        </button>
                    </a>
                    @if(Auth::user()->role_id == 1 )
                    <button wire:click="showEditServiceModal({{ $service->id }})" wire:loading.attr="disabled" class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-80 w-24 rounded-lg text-xs font-center px-6 py-2 inline-flex  items-center me-1 mb-2">
                        <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                          </svg>
                        Edit
                    </button>
                    @endif

                    @if($service->is_hidden == 0)  <!-- Active employee -->
                    <button wire:click="archiveService({{ $service->id }})" class="text-white bg-gradient-to-r from-red-400 via-red-500 to-red-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 rounded-lg text-xs px-4 py-2 inline-flex items-center me-1 mb-2">
                        <svg class="w-4 h-4 me-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                          </svg>

                        Archive
                    </button>
                @endif
                @if ($service->is_hidden == 1) <!-- Only show if archived -->
                    <button wire:click="unarchiveService({{ $service->id }})" class="text-white bg-gradient-to-r from-cyan-400 via-cyan-500 to-cyan-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-cyan-300 dark:focus:ring-cyan-800 rounded-lg text-xs px-5 py-2.5  inline-flex text-center me-2 mb-2">
                        <svg class="w-4 h-4 me-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m6 4.125 2.25 2.25m0 0 2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                          </svg>

                        Unarchive
                    </button>
                @endif




                </div>
            </td>
        </tr>
        @endforeach

      </tbody>
    </table>
    <div class="p-5">
      {{ $services->links() }}
    </div>


    <x-dialog-modal wire:model="showEditServiceModal">
        <x-slot name="title">{{ __('Edit Service') }}</x-slot>
        <x-slot name="content">
            @include('components.service-form', ['isEditing' => true])
        </x-slot>
        <x-slot name="footer">
            <div class="flex gap-3">
            <x-secondary-button wire:click="$set('showEditServiceModal', false)">Cancel</x-secondary-button>
            <x-button wire:click="saveService">Save Changes</x-button>
            </div>
        </x-slot>
    </x-dialog-modal>


    <x-dialog-modal wire:model="showAddServiceModal">
        <x-slot name="title">{{ __('Add Service') }}</x-slot>
        <x-slot name="content">
            @include('components.service-form', ['isEditing' => false])
        </x-slot>
        <x-slot name="footer">
            <div class="flex gap-3">
            <x-secondary-button wire:click="$set('showAddServiceModal', false)">Cancel</x-secondary-button>
            <x-button wire:click="saveService">Save</x-button>
            </div>
        </x-slot>
    </x-dialog-modal>



    <script>
     document.addEventListener('DOMContentLoaded', function () {
    // Archive Confirmation
        Livewire.on('confirmArchive', (serviceId) => {
            Swal.fire({
                title: 'Are you sure?',
                text: "This service will be archived!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, archive it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.emit('confirmArchiveService', serviceId); // Trigger Livewire method
                    Swal.fire(
                        'Archived!',
                        'The service has been archived.',
                        'success'
                    );
                }
            });
        });

        // Unarchive Confirmation
        Livewire.on('confirmUnarchive', (serviceId) => {
            Swal.fire({
                title: 'Are you sure?',
                text: "This service will be unarchived!",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, unarchive it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.emit('confirmUnarchiveService', serviceId); // Trigger Livewire method
                    Swal.fire(
                        'Unarchived!',
                        'The service has been unarchived.',
                        'success'
                    );
                }
            });
        });
    });
    </script>


</div>
</div>
