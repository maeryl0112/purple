@php
    use App\Enums\UserRolesEnum;
    $role = UserRolesEnum::from(Auth::user()->role_id)->name;
@endphp
<x-app-layout>

     <livewire:customer-view-appointment />

</x-app-layout>
