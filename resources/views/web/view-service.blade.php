<x-app-layout>

<div class="bg-white flex items-center justify-center md:h-screen p-4">
      <div class="shadow-[0_2px_16px_-3px_rgba(106,38,149,0.3)] max-w-6xl max-md:max-w-lg rounded-md pt-10 p-6">
        <div class="grid md:grid-cols-2 items-center ">
          <div class="max-md:order-1 lg:min-w-[450px]">
            <img src="{{ asset('storage/'. $service->image) }}" alt="{{$service->name . ' image'}}" class="lg:w-11/12 w-full object-cover"/>
          </div>

          <div class="md:max-w-md w-full mx-auto">
            <div class="mt-5 mb-2">
              <h3 class="text-3xl font-extrabold text-salonPurple">{{$service->name}}</h3>
              <span class="text-gray-600"> Description : {{ $service->description }}</span>
              <br>
              <span class="text-gray-600"> Allergen : {{ $service->allergens }}</span>
              <br>
              <span class="text-gray-600"> Category : {{ $service->category->name }}</span>
            </div>
          
            <section aria-labelledby="information-heading">
                        <h3 id="information-heading" class="sr-only">Product information</h3>

                        <p class="text-2xl font-bold text-gray-900 mb-2">₱ {{ number_format($service->price, 0, '.', ',') }}
                        </p>


                            @if (Auth::user()?->role_id == 1 || Auth::user()?->role_id == 2)

                            <a href="{{ route('manageservices') }}?search={{ $service->slug }}">
                                <x-button class="px-5 py-2 text-white bg-purple-500 rounded-md hover:bg--600">
                                    Manage
                                </x-button>
                            </a>

                                <div class="bg-gray-100 px-3 py-2 my-2  over-flow-auto">
                                    <span class="font-semibold"> Analytics insights </span>
                                    <table class="border-collapse w-full">
                                        <thead>
                                        <tr>
                                            <th class="border p-2">Metric</th>
                                            <th class="border p-2">Last Week</th>
                                            <th class="border p-2">Change <span class="text-sm block">Last Week</span></th>
                                            <th class="border p-2">Total</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td class="border p-2">Views</td>
                                            <td class="border p-2">{{ $viewsLastWeek }}</td>
                                            <td class="border p-2">
                                                @if($percentageViewsChangeLastWeek === 'N/A')
                                                    {{ $percentageViewsChangeLastWeek }}
                                                @elseif($percentageViewsChangeLastWeek > 0)
                                                    <span class="text-green-800"><span class="text-2xl">↑</span> {{ $percentageViewsChangeLastWeek }} %</span>
                                                @elseif ($percentageViewsChangeLastWeek < 0)
                                                    <span class="text-red-800"><span class="text-2xl">↓</span> {{ $percentageViewsChangeLastWeek }} %</span>
                                                @else
                                                    {{ $percentageViewsChangeLastWeek }} %
                                                @endif
                                            </td>
                                            <td class="border p-2">{{ $views }}</td>
                                        </tr>

                                        <tr>
                                            <td class="border p-2">Appointments</td>
                                            <td class="border p-2">{{ $appointmentsLastWeek }}</td>
                                            <td class="border p-2">
                                                @if($percentageAppointmentsChangeLastWeek === 'N/A')
                                                    {{ $percentageAppointmentsChangeLastWeek }}
                                                @elseif($percentageAppointmentsChangeLastWeek > 0)
                                                    <span class="text-green-800"><span class="text-2xl">↑</span> {{ $percentageAppointmentsChangeLastWeek }} %</span>
                                                @elseif ($percentageAppointmentsChangeLastWeek < 0)
                                                    <span class="text-red-800"><span class="text-2xl">↓</span> {{ $percentageAppointmentsChangeLastWeek }} %</span>
                                                @else
                                                    {{ $percentageAppointmentsChangeLastWeek }} %
                                                @endif
                                            </td>
                                            <td class="border p-2">{{ $appointmentsTotal }}</td>
                                        </tr>
                                        <tr>
                                            <td class="border p-2">Appointments (Last Month)</td>
                                            <td class="border p-2">{{ $appointmentsLastMonth }}</td>
                                            <td class="border p-2">
                                                @if($percentageAppointmentsChangeLastMonth === 'N/A')
                                                    {{ $percentageAppointmentsChangeLastMonth }}
                                                @elseif($percentageAppointmentsChangeLastMonth > 0)
                                                    <span class="text-green-800"><span class="text-2xl">↑</span> <span class="text-2xl">{{ $percentageAppointmentsChangeLastMonth }} %</span></span>
                                                @elseif ($percentageAppointmentsChangeLastMonth < 0)
                                                    <span class="text-red-800"><span class="text-2xl">↓</span> <span class="text-2xl">{{ $percentageAppointmentsChangeLastMonth }} %</span></span>
                                                @endif
                                                <span class="text-[12px] block">Monthly</span>
                                            </td>
                                            <td class="border p-2"></td>
                                        </tr>
                                        <tr>
                                            <td class="border p-2">Revenue</td>
                                            <td class="border p-2"> PHP {{ number_format($totalRevenueLastWeek, 2, '.', ',') }}</td>
                                            <td class="border p-2">
                                                @if($percentageRevenueChangeLastWeek === 'N/A')
                                                    {{ $percentageRevenueChangeLastWeek }}
                                                @elseif($percentageRevenueChangeLastWeek > 0)
                                                    <span class="text-green-800"><span class="text-2xl">↑</span> {{ $percentageRevenueChangeLastWeek }} %</span>
                                                @elseif ($percentageRevenueChangeLastWeek < 0)
                                                    <span class="text-red-800"><span class="text-2xl">↓</span> {{ $percentageRevenueChangeLastWeek }} %</span>
                                                @endif
                                            </td>
                                            <td class="border p-2">PHP {{ number_format($totalRevenue, 2, '.', ',') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="border p-2">Revenue (Last Month)</td>
                                            <td class="border p-2">PHP {{ number_format($totalRevenueLastMonth, 2, '.', ',') }}</td>
                                            <td class="border p-2">
                                                @if($percentageRevenueChangeLastMonth === 'N/A')
                                                    {{ $percentageRevenueChangeLastMonth }}
                                                @elseif($percentageRevenueChangeLastMonth > 0)
                                                    <span class="text-green-800"><span class="text-2xl">↑</span> <span class="text-2xl">{{ $percentageRevenueChangeLastMonth }} %</span></span>
                                                @elseif ($percentageRevenueChangeLastMonth < 0)
                                                    <span class="text-red-800"><span class="text-2xl">↓</span> <span class="text-2xl">{{ $percentageRevenueChangeLastMonth }} %</span></span>
                                                @endif
                                                <span class="text-[12px] block">Monthly</span>
                                            </td>
                                            <td class="border p-2"></td>
                                        </tr>
                                        </tbody>
                                    </table>




                                </div>

                                    @endif

                                    
                

                </section>

                @if (Auth::user()?->role_id == 3 )
                    <livewire:adding-service-to-cart :service="$service"/>
                @endif
            </div>
        </div>
      </div >
    </div>



</x-app-layout>
