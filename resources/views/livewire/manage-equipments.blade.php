<div class="p-2 sm:ml-64">
    <div class="flex justify-between mx-7">
        <h2 class="text-2xl font-bold text-salonPurple">MANAGE  EQUIPMENTS</h2>

    </div>
    @if (session()->has('message'))
        <script>
            window.addEventListener('equipmentAddedOrUpdated', event => {
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

            <button  wire:click="showAddEquipmentModal"  type="button" class="focus:outline-none text-white bg-salonPurple hover:bg-darkPurple focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">ADD</button>
            @if(Auth::user()->role_id == 1 )
            <button wire:click="exportToPdf" class="focus:outline-none text-white bg-salonPurple hover:bg-darkPurple focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">Download to PDF</button>
            @endif

            <label for="default-search" class="my-2 text-sm font-medium text-gray-900 sr-only ">Search</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <input type="search" wire:model="search" id="default-search" name="search" class="block w-full p-4 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-purple-500 focus:border-purple-500" placeholder="Search Equipment...">
             
            </div>

            <div class="pt-2.5">
                

                <select id="categoryFilter" wire:model="categoryFilter"  class="border text-gray-900 px-5 pt-2.5 me-2  border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    <option value="" selected>All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>

            <select wire:model="statusFilter" class="border text-gray-900  px-5 pt-2.5 me-2 border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                <option value="active">Active</option>
                <option value="archived">Archived</option>
            </select>

            @if(Auth::user()->role_id == 1 )
            <select id="branchFilter" wire:model="branchFilter"  class="border text-gray-900 px-5 pt-2.5 me-2  border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                    <option value="" selected>All Branch<i class="fas fa-code-branch    "></i></option>
                    @foreach ($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
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
              <th scope="col" class="px-4 py-4 font-large text-gray-900">Brand</th>
              <th scope="col" class="px-6 py-4 font-large text-gray-900">Category</th>
              <th scope="col" class="px-6 py-4 font-large text-gray-900">Quantity</th>
              <th scope="col" class="px-6 py-4 font-large text-gray-900">Last Maintenance</th>
              <th scope="col" class="px-6 py-4 font-large text-gray-900">Next Maintenance</th>
              <th scope="col" class="px-6 py-4 font-large text-gray-900">Branch</th>
              <th scope="col" class="px-6 py-4 font-large text-gray-900">Assigned Staff</th>
              <th scope="col" class="px-6 py-4 font-large text-gray-900">Status</th>
              <th scope="col" class="px-6 py-4 font-large text-gray-900">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 border-t border-gray-100">

            @foreach ($equipments as $equipment)
            <tr class="hover:bg-gray-50">
                <td class="pl-6 py-4  max-w-0">{{ $equipment->id }}</td>

                <td class="px-6 py-4  max-w-0">
                    <div class="font-medium text-gray-700">
                        <img src="{{ asset('storage/' . $equipment->image) }}" alt="" class="w-20 h-20 object-cover">
                    </div>
                </td>


                <td class="px-6 py-4  max-w-0">
                    <div class="font-medium text-gray-900">{{ $equipment->name}}</div>
                </td>

                <td class="px-6 py-4  max-w-0">
                    <div class="font-medium text-gray-900">{{ $equipment->brand_name}}</div>
                </td>

                <td class="px-6 py-4 max-w-0">
                    <div class="'font-medium text-gray-900" >{{ $equipment->category?->name }}</div>
                </td>



                <td class="px-6 py-4 max-w-0">
                    <div class="font-medium {{ $equipment->quantity <= 5 ? 'text-red-600' : 'text-gray-700' }}">
                        {{ $equipment->quantity }}
                    </div>
                </td>


                <td class="px-6 py-4  max-w-0">
                    <div class="font-medium text-gray-700">{{ $equipment->last_maintenance}}</div>
                </td>

                <td class="px-6 py-4  max-w-0">
                    <div class="font-medium text-gray-700">{{ $equipment->next_maintenance}}</div>
                </td>

                    <td class="px-6 py-4  max-w-0">
                        <div class="font-medium text-gray-700">{{ $equipment->branch ? $equipment->branch->name : 'No Branch Assigned' }}</div>
                    </td>

                <td class="px-6 py-4  max-w-0">
                    <div class="font-medium text-gray-700">{{ $equipment->employee?->first_name}}</div>
                </td>

                <td class="px-6 py-2">
                    @if($equipment->status == true)
                        <span
                            class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2 py-1 text-xs font-medium  text-green-600"
                        >
                <span class="h-1.5 w-1.5 rounded-full bg-green-600"></span>
                Active
              </span>

                    @else
                        <span
                            class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-600"
                        >
                <span class="h-1.5 w-1.5 rounded-full bg-red-600"></span>
                Archived
              </span>

                    @endif
                </td>

                    <td class="px-6 py-4 gap-2">
                        <button wire:click="viewEquipment({{ $equipment->id }})" class="text-white bg-gradient-to-r from-purple-500 via-purple-600 to-purple-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-purple-300 dark:focus:ring-purple-800 rounded-lg text-xs w-24 px-6 py-2 inline-flex items-center me-1 mb-2">
                            <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                              </svg>
                            View
                        </button>

                        @if(Auth::user()->role_id == 1 )
                        <button wire:click="showEditEquipmentModal({{ $equipment->id }})" wire:loading.attr="disabled" class="text-white bg-gradient-to-r from-green-400 via-green-500 to-green-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 rounded-lg text-xs w-24 px-6 py-2 inline-flex items-center me-1 mb-2">
                            <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                              </svg>
                            Edit
                        </button>
                        @endif

                        @if($equipment->status == 1)  <!-- Active employee -->
                        <button wire:click="archiveEquipment({{ $equipment->id }})" class="text-white bg-gradient-to-r from-red-400 via-red-500 to-red-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 rounded-lg text-xs px-4 py-2 inline-flex items-center me-1 mb-2">
                            <svg class="w-4 h-4 me-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                              </svg>

                            Archive
                        </button>
                    @endif
                    @if($equipment->status == 0)   <!-- Archived employee -->
                        <button wire:click="unarchiveEquipment({{ $equipment->id }})" class="text-white bg-gradient-to-r from-cyan-400 via-cyan-500 to-cyan-600 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-cyan-300 dark:focus:ring-cyan-800 rounded-lg text-xs px-5 py-2.5  inline-flex text-center me-2 mb-2">
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
          {{ $equipments->links() }}
        </div>



        <x-dialog-modal wire:model="confirmingEquipmentDeletion">
            <x-slot name="title">
                {{ __('Delete Equipment') }}
            </x-slot>

            <x-slot name="content">
                {{ __('Are you sure you want to Delete the Equipment?') }}

            </x-slot>

            <x-slot name="footer">
                <div class="flex gap-3">
                <x-secondary-button wire:click="$set('confirmingEquipmentDeletion', false)" wire:loading.attr="disabled">
                    {{ __('Cancel') }}
                </x-secondary-button>

                    <x-danger-button wire:click="deleteEquipment({{ $confirmingEquipmentDeletion }})" wire:loading.attr="disabled">
                        {{ __('Delete') }}
                    </x-danger-button>
                </div>

            </x-slot>
        </x-dialog-modal>

        <x-dialog-modal wire:model="confirmingEquipmentView">
            <x-slot name="title">
                {{ __('Equipment Details') }}
            </x-slot>

            <x-slot name="content">
                @if($equipment)
                      <div class="w-full md:w-1/3 bg-white grid place-items-center">
                        <img src="{{ asset('storage/' . $equipment->image) }}" alt="Product Image" class="rounded-xl" />
                      </div>
                      <div class="w-full md:w-2/3 bg-white flex flex-col space-y-2 p-3">
                        <div class="flex justify-between item-center">
                        <h3 class="font-black text-gray-800 md:text-3xl text-xl">{{ $equipment->name }} </h3>
                        <p class="md:text-lg text-gray-500 text-base"> {{ $equipment->brand_name }}</p>
                        <p class="text-xl font-black text-gray-800"> {{ $equipment->category->name ?? 'N/A' }}</p>
                        <p class="text-xl font-black text-gray-800">{{ $equipment->quantity}}</p>
                        <p class="text-xl font-black text-gray-800">{{ $equipment->last_maintenance }}</p>
                        <p class="text-xl font-black text-gray-800">{{ $equipment->next_maintenance }}</p>
                        <p class="text-xl font-black text-gray-800">{{ $equipment->purchased_date }}</p>
                        <p class="text-xl font-black text-gray-800">{{ $equipment->employee->first_name ?? 'N/A' }}<</p>
                       </div>
                      </div>
                @else
                    <p>{{ __('Equipment details not available.') }}</p>
                @endif
            </x-slot>

            <x-slot name="footer">
                <x-secondary-button wire:click="$set('confirmingEquipmentView', false)" wire:loading.attr="disabled">
                    {{ __('Close') }}
                </x-secondary-button>
            </x-slot>
        </x-dialog-modal>

        <x-dialog-modal wire:model="showAddEquipmentModal">
            <x-slot name="title">{{ __('Add Equipment') }}</x-slot>
            <x-slot name="content">
                @include('components.equipment-form', ['isEditing' => false])
            </x-slot>
            <x-slot name="footer">
                <div class="flex gap-3">
                <x-secondary-button wire:click="closeModals">Cancel</x-secondary-button>
                <x-button wire:click="saveEquipment">Save</x-button>
                </div>
            </x-slot>
        </x-dialog-modal>


        <x-dialog-modal wire:model="showEditEquipmentModal">
            <x-slot name="title">{{ __('Edit Equipment') }}</x-slot>
            <x-slot name="content">
                @include('components.equipment-form', ['isEditing' => true])
            </x-slot>
            <x-slot name="footer">
                <div class="flex gap-3">
                <x-secondary-button wire:click="closeModals">Cancel</x-secondary-button>
                <x-button wire:click="saveEquipment">Save Changes</x-button>
                </div>
            </x-slot>
        </x-dialog-modal>



        <script>
   
   window.addEventListener('downloadFile', event => {
                const link = document.createElement('a');
                link.href = event.detail.url;
                link.download = 'equipment-report.pdf'; // Optional: Customize the filename
                document.body.appendChild(link);
                link.click();
                link.remove();
            });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
     Livewire.on('confirmArchive', (equipmentId) => {
         Swal.fire({
             title: 'Are you sure?',
             text: "This Equipment will be archived!",
             icon: 'warning',
             showCancelButton: true,
             confirmButtonColor: '#3085d6',
             cancelButtonColor: '#d33',
             confirmButtonText: 'Yes, archive it!'
         }).then((result) => {
             if (result.isConfirmed) {
                 Livewire.emit('confirmArchiveEquipment', equipmentId);
                 Swal.fire(
                     'Archived!',
                     'The Equipment has been archived.',
                     'success'
                 );
             }
         });
     });

     Livewire.on('confirmUnarchive', (equipmentId) => {
         Swal.fire({
             title: 'Are you sure?',
             text: "This Equipment will be unarchived!",
             icon: 'info',
             showCancelButton: true,
             confirmButtonColor: '#3085d6',
             cancelButtonColor: '#d33',
             confirmButtonText: 'Yes, unarchive it!'
         }).then((result) => {
             if (result.isConfirmed) {
                 Livewire.emit('confirmUnarchiveEquipment', equipmentId);
                 Swal.fire(
                     'Unarchived!',
                     'The Equipment has been unarchived.',
                     'success'
                 );
             }
         });
     });
  

       
 });
 </script>


    </div>
</div>






