


<div class="p-4 sm:ml-64">
    <div class="flex justify-between mx-7">
        <h2 class="text-2xl font-bold text-salonPurple">
            MANAGE  CONSUMABLES</h2>
    </div>

    <div class="fixed top-5 right-4 z-50 space-y-4">
            @foreach ($nearExpirationSupplies as $supply)
                <div x-data="{ show: true }" x-show="show" x-transition
                    id="alert-expiration-{{ $supply->id }}"
                    class="p-4 mb-4 text-red-800 border border-red-300 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 dark:border-red-800 shadow-lg"
                    role="alert">
                <div class="flex items-center">
                    <svg class="flex-shrink-0 w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span class="sr-only">Info</span>
                    <h3 class="text-lg font-medium">Warning: Expiring Soon</h3>
                </div>
                <div class="mt-2 mb-4 text-sm">
                    Consumable item <strong>{{ $supply->name }}</strong> is nearing its expiration date on <strong>{{ $supply->expiration_date }}</strong>.<br> Please review and reorder if necessary.
                </div>
                <div class="flex">
               
                    <button @click="show = false"
                            type="button"
                            class="text-red-800 bg-transparent border border-red-800 hover:bg-red-900 hover:text-white focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-xs px-3 py-1.5 text-center dark:hover:bg-red-600 dark:border-red-600 dark:text-red-500 dark:hover:text-white dark:focus:ring-red-800"
                            aria-label="Close">
                    Dismiss
                    </button>
                </div>
                </div>
            @endforeach
            @foreach ($lowQuantitySupplies as $supply)
            <div x-data="{ show: true }" x-show="show" x-transition
                id="alert-low-quantity-{{ $supply->id }}"
                class="p-4 mb-4 text-yellow-800 border border-yellow-300 rounded-lg bg-yellow-50 dark:bg-gray-800 dark:text-yellow-400 dark:border-yellow-800 shadow-lg"
                role="alert">
                <div class="flex items-center">
                    <svg class="flex-shrink-0 w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 0C4.612 0 0 5.336 0 7c0 1.742 3.546 7 10 7 6.454 0 10-5.258 10-7 0-1.664-4.612-7-10-7Zm0 10a3 3 0 1 1 0-6 3 3 0 0 1 0 6Z"/>
                    </svg>
                    <span class="sr-only">Info</span>
                    <h3 class="text-lg font-medium">Warning: Low Quantity</h3>
                </div>
                <div class="mt-2 mb-4 text-sm">
                    Consumable item <strong>{{ $supply->name }}</strong> has low quantity: <strong>{{ $supply->quantity }}</strong>. <br>Please review and reorder if necessary.
                </div>
                <div class="flex">
                    <button @click="show = false"
                            type="button"
                            class="text-yellow-800 bg-transparent border border-yellow-800 hover:bg-yellow-900 hover:text-white focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-xs px-3 py-1.5 text-center dark:hover:bg-yellow-600 dark:border-yellow-600 dark:text-yellow-500 dark:hover:text-white dark:focus:ring-yellow-800"
                            aria-label="Close">
                        Dismiss
                    </button>
                </div>
            </div>
        @endforeach
        </div> 

    <div class="overflow-auto rounded-lg border border-gray-200 shadow-md m-5">

        <div class="w-full m-4 flex">
            <div class="w-1/2 mx-2">

                <button  wire:click="openAddSuppliesModal" type="button" class="focus:outline-none text-white bg-salonPurple   hover:bg-darkPurple focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">ADD</button>
                @if(Auth::user()->role_id == 1 )
                <button  wire:click="exportToPdf"  class="focus:outline-none text-white bg-salonPurple hover:bg-darkPurple focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">Download to PDF</button>
                @endif
            <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only ">Search</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input type="search" wire:model="search" id="default-search" name="search" class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-purple-500 focus:border-purple-500" placeholder="Search Supplies...">
          
            </div>

            <div class="py-2.5 me-2">
            <select id="categoryFilter" wire:model="categoryFilter" class="border text-gray-900 px-5 pt-2.5 me-2 border-gray-300 focus:ring-purple-500 focus:border-purple-500   rounded-lg">
                <option  value="" selected>All Category</option>
                @foreach ($categories as $category)
                <option value="{{$category->id}}">{{$category->name}}</option>
                @endforeach
              </select>


             <select class="border text-gray-900 px-5 pt-2.5 me-2 border-gray-300 focus:ring-purple-500 focus:border-purple-500 rounded-lg" wire:model="selectFilter" >
                <option value="all">All</option>
                <option value="expired">Expired</option>
                <option value="low_quantity">Low Quantity</option>
          </select>

            <select wire:model="statusFilter" class="border text-gray-900 px-5 pt-2.5 me-2 border-gray-300 focus:ring-purple-500 focus:border-purple-500 rounded-lg">
                <option value="active">Active</option>
                <option value="archived">Archived</option>
            </select>

            @if(Auth::user()->role_id == 1 )
            <select id="branchFilter" wire:model="branchFilter" class="border text-gray-900 px-5 pt-2.5 me-2 border-gray-300 focus:ring-purple-500 focus:border-purple-500   rounded-lg">
                <option value="">All Branch</option>
                @foreach ($branches as $branch)
                <option value="{{$branch->id}}">{{$branch->name}}</option>
                @endforeach
              </select>
              @endif
            </div>

            </div>
        </div>

        <table class="w-full border-collapse bg-white text-left text-sm text-gray-500 overflow-x-scroll min-w-screen">
          <thead class="bg-gray-50">
            <tr>
              <th scope="col" class="pl-6 py-4 font-large text-gray-900">Id</th>
              <th scope="col" class="px-6 py-4 font-large text-gray-900">Image</th>
              <th scope="col" class="px-4 py-4 font-large text-gray-900">Name</th>
              <th scope="col" class="px-6 py-4 font-large text-gray-900">Color Code</th>
              <th scope="col" class="px-6 py-4 font-large text-gray-900">Color Shade</th>
              <th scope="col" class="px-6 py-4 font-large text-gray-900">Category</th>
              <th scope="col" class="px-6 py-4 font-large text-gray-900">Quantity</th>
              <th scope="col" class="px-6 py-4 font-large text-gray-900">Size</th>
              <th scope="col" class="px-6 py-4 font-large text-gray-900">Expiration Date</th>
              <th scope="col" class="px-6 py-4 font-large text-gray-900">Branch</th>
              <th scope="col" class="px-6 py-4 font-large text-gray-900">Status</th>
              <th scope="col" class="px-6 py-4 font-large text-gray-900">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 border-t border-gray-100">

            @foreach ($supplies as $supply)
            <tr class="hover:bg-gray-50">
                <td class="pl-6 py-4  max-w-0">{{ $supply->id }}</td>

                <td class="px-6 py-4  max-w-0">
                    <div class="font-medium text-gray-700">
                        <img src="{{ asset('storage/' . $supply->image) }}" alt="" class="w-20 h-20 object-cover">
                    </div>
                </td>


                <td class="px-6 py-4  max-w-0">
                    <div class="font-medium text-gray-900">{{ $supply->name}}</div>
                </td>

             

                <td class="px-6 py-4  max-w-0">
                    <div class="font-medium text-gray-900">{{ $supply->color_code}}</div>
                </td>

                <td class="px-6 py-4  max-w-0">
                    <div class="font-medium text-gray-900">{{ $supply->color_shade}}</div>
                </td>

                <td class="px-6 py-4 max-w-0">
                    <div class="'font-medium text-gray-900" >{{ $supply->category?->name }}</div>
                </td>



                <td class="px-6 py-4 max-w-0">
                    <div class="font-medium {{ $supply->quantity <= 5 ? 'text-red-600' : 'text-gray-700' }}">
                        {{ $supply->quantity }}
                    </div>
                </td>



                <td class="px-6 py-4  max-w-0">
                    <div class="font-medium text-gray-700">{{ $supply->size}}</div>
                </td>

                <td class="px-6 py-4 {{ \Carbon\Carbon::parse($supply->expiration_date)->diffInDays(now()) <= 7 ? 'text-red-600' : 'text-gray-700' }}">
                    {{ $supply->expiration_date }}
                </td>
                <td class="px-6 py-4 max-w-0">
                    <div class="'font-medium text-gray-900" >{{ $supply->branch->name }}</div>
                </td>

                <td class="px-6 py-4">
                <span class="{{ $supply->status ?  'bg-green-50 text-green-600' : 'bg-red-50 text-red-600' }} px-2 py-1 text-xs font-medium rounded-full">
                    {{ $supply->status ?   'Active' : 'Archived'}}
            </td>

                <td class="px-6 py-4 gap-2">

                        <button wire:click="viewSupplies({{ $supply->id }})" class="text-white bg-gradient-to-r from-purple-500 via-purple-600 to-purple-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-purple-300 dark:focus:ring-purple-800 rounded-lg text-xs w-24 px-6 py-2 inline-flex items-center me-1 mb-2">
                            <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                              </svg>
                            View
                        </button>

                        @if(Auth::user()->role_id == 1 )
                        <button wire:click="showEditSuppliesModal({{ $supply->id }})" wire:loading.attr="disabled" class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 dark:shadow-lg dark:shadow-green-800/80  rounded-lg text-xs w-24 px-6 py-2 inline-flex items-center me-1 mb-2">
                            <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                              </svg>
                           Edit
                        </button>
                        @endif

                        @if($supply->status == 1)  <!-- Active employee -->
                        <button wire:click="archiveSupplies({{ $supply->id }})" class="text-white bg-gradient-to-r from-red-400 via-red-500 to-red-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 rounded-lg text-xs px-4 py-2 inline-flex items-center me-1 mb-2">
                            <svg class="w-4 h-4 me-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                              </svg>

                            Archive
                        </button>
                    @endif  <!-- Archived employee -->
                    @if($supply->status == 0)
                        <button wire:click="unarchiveSupplies({{ $supply->id }})" class="text-white bg-gradient-to-r from-cyan-400 via-cyan-500 to-cyan-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-cyan-300 dark:focus:ring-cyan-800 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2">
                            <svg class="w-4 h-4 me-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m6 4.125 2.25 2.25m0 0 2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                              </svg>

                            Unarchive
                        </button>
                    @endif
                </td>
            </tr>
            @endforeach
          </tbody>
        </table>

        <div class="p-5">
          {{ $supplies->links() }}
        </div>



        <x-dialog-modal wire:model="confirmingSuppliesDeletion">
            <x-slot name="title">
                {{ __('Delete Supply') }}
            </x-slot>

            <x-slot name="content">
                {{ __('Are you sure you want to Delete the Supplies?') }}

            </x-slot>

            <x-slot name="footer">
                <div class="flex gap-3">
                <x-secondary-button wire:click="$set('confirmingSuppliesDeletion', false)" wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </x-secondary-button>

                    <x-danger-button wire:click="deleteSupplies({{ $confirmingSuppliesDeletion }})" wire:loading.attr="disabled">
                        {{ __('Delete') }}
                    </x-danger-button>
                </div>

            </x-slot>
        </x-dialog-modal>

        <x-dialog-modal wire:model="confirmingSuppliesView">
            <x-slot name="title">
                {{ __('Supplies Details') }}
            </x-slot>
            <x-slot name="content">
                <!-- Display supply details -->
                @if($supply)
                <div class="w-full md:w-1/3 bg-white grid place-items-center">
                    <img src="{{ asset('storage/' . $supply->image) }}" alt="Supply Image" class="w-20 h-20 object-cover lazyload" loading="lazy">
                    <div class="w-full md:w-2/3 bg-white flex flex-col space-y-2 p-3">
                        <h3 class="font-black text-gray-800">{{ $supply->name }}</h3>
                        <p class="text-xl font-black text-gray-800">{{ $supply->category->name ?? 'N/A' }}</p>
                        <p class="text-xl font-black text-gray-800">{{ $supply->description }}</p>
                        <p class="text-xl font-black text-gray-800">{{ $supply->quantity }}</p>
                        <p class="text-xl font-black text-gray-800">{{ $supply->size }}</p>
                        <p class="text-xl font-black text-gray-800">{{ $supply->color_code }}</p>
                        <p class="text-xl font-black text-gray-800">{{ $supply->color_shade }}</p>
                    </div>
                </div>
                @endif
            </x-slot>
            <x-slot name="footer">
                <x-secondary-button wire:click="$set('confirmingSuppliesView', false)" wire:loading.attr="disabled">
                    {{ __('Close') }}
                </x-secondary-button>
            </x-slot>
        </x-dialog-modal>


        <x-dialog-modal wire:model="showAddSuppliesModal">
            <x-slot name="title">{{ __('Add Supply') }}</x-slot>
            <x-slot name="content">
                @include('components.supplies-form', ['isEditing' => true])
            </x-slot>
            <x-slot name="footer">
            <div class="flex gap-3">
                <x-secondary-button wire:click="closeModals">Cancel</x-secondary-button>
                <x-button wire:click="saveSupplies">Save</x-button>
            </div>
            </x-slot>
        </x-dialog-modal>

        <x-dialog-modal wire:model="showEditSuppliesModal">
            <x-slot name="title">{{ __('Edit Supply') }}</x-slot>
            <x-slot name="content">
                @include('components.supplies-form', ['isEditing' => true])
            </x-slot>
            <x-slot name="footer">
            <div class="flex gap-3">
                <x-secondary-button wire:click="closeModals">Cancel</x-secondary-button>
                <x-button wire:click="saveSupplies">Save Changes</x-button>
            </div>
            </x-slot>
        </x-dialog-modal>

        @if (session()->has('message'))
        <script>
            window.addEventListener('suppliesAddedOrUpdated', event => {
                Swal.fire({
                    title: 'Success!',
                    text: "{{ session('message') }}",
                    icon: 'success',
                    confirmButtonText: 'OK'
                });
            });
        </script>
    @endif

    <script>
    window.addEventListener('downloadFile', event => {
        const link = document.createElement('a');
        link.href = event.detail.url;
        link.setAttribute('download', 'supplies-report.pdf'); // Ensures the filename is set
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link); // Cleanup after download
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
     Livewire.on('confirmArchive', (supplyId) => {
         Swal.fire({
             title: 'Are you sure?',
             text: "This Supply will be archived!",
             icon: 'warning',
             showCancelButton: true,
             confirmButtonColor: '#3085d6',
             cancelButtonColor: '#d33',
             confirmButtonText: 'Yes, archive it!'
         }).then((result) => {
             if (result.isConfirmed) {
                 Livewire.emit('confirmArchiveSupplies', supplyId);
                 Swal.fire(
                     'Archived!',
                     'The Supply has been archived.',
                     'success'
                 );
             }
         });
     });

     Livewire.on('confirmUnarchive', (supplyId) => {
         Swal.fire({
             title: 'Are you sure?',
             text: "This Supply will be unarchived!",
             icon: 'info',
             showCancelButton: true,
             confirmButtonColor: '#3085d6',
             cancelButtonColor: '#d33',
             confirmButtonText: 'Yes, unarchive it!'
         }).then((result) => {
             if (result.isConfirmed) {
                 Livewire.emit('confirmUnarchiveSupplies', supplyId);
                 Swal.fire(
                     'Unarchived!',
                     'The Supply has been unarchived.',
                     'success'
                 );
             }
         });
     });
 });
 </script>
    </div>
</div>
