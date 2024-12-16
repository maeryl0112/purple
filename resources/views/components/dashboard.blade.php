<x-app-layout>

  <div class="py-4">        
        <aside id="sidebar" class="fixed top-0 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700" aria-label="Sidebar">
            <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
                <div class="min-h-screen flex flex-col flex-auto flex-shrink-0 antialiased bg-gray-50 text-gray-800">
                    <div class="fixed flex flex-col top-0 left-0 w-64 bg-white h-full ">
                      <div class="flex items-center justify-center h-14 ">

                      </div>
                      <div class="overflow-y-auto overflow-x-hidden flex-grow">

                        <ul class="flex flex-col py-4 space-y-1">
                          <li class="px-5">
                            <div class="flex flex-row items-center h-8">
                              <div class="text-sm font-light tracking-wide text-gray-500">Menu</div>
                            </div>
                          </li>
                          <li>
                            <a href="/dashboard" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-100 text-gray-600 hover:text-gray-800 border-l-4 border-transparent hover:border-purple-500 pr-6">
                              <span class="inline-flex justify-center items-center ml-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                              </span>
                              <span class="ml-2 text-left text-sm tracking-wide truncate">Dashboard</span>
                            </a>
                          </li>

                          {{-- User Role 1= Admin, 2 = Employee --}}
                          @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2)

                          <li class="px-5">
                            <div class="flex flex-row items-center h-8">
                              <div class="text-sm font-light tracking-wide text-gray-500">CRM Manage</div>
                            </div>
                          </li>

                          <li>
                            <a href="{{ route('manageusers') }}"
                               class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-100 text-gray-600 hover:text-gray-800 border-l-4 border-transparent hover:border-darkPurple pr-6
                               {{ request()->routeIs('manageusers') ? 'bg-purple-200 border-purple-500 rounded-lg text-gray-900 font-semibold' : '' }}">
                                <span class="inline-flex justify-center items-center ml-4">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                </span>
                                <span class="ml-2 text-sm tracking-wide truncate">Manage Users</span>
                            </a>
                        </li>







                        <li>
                            <a href="{{ route('manageappointments') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-100 text-gray-600 hover:text-gray-800 border-l-4 border-transparent hover:border-darkPurple pr-6
                         {{ request()->routeIs('manageappointments') ? 'bg-purple-300 border-purple-500 rounded-lg text-gray-900 font-semibold': '' }}
                        ">
                          <span class="inline-flex justify-center items-center ml-4">
                            <svg fill="currentColor" class="w-5 h-5" viewBox="0 0 24 24" data-name="Layer 1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"><title/><path d="M18,5V3a1,1,0,0,0-2,0V5H8V3A1,1,0,0,0,6,3V5H2V21H22V5Zm2,14H4V7H20Zm-7-9H11v2h2Zm4,0H15v2h2ZM9,14H7v2H9Zm4,0H11v2h2Z"/></svg>
                          </span>
                                <span class="ml-2 text-sm tracking-wide truncate">Manage Appointments</span>

                            </a>
                        </li>

                          <li>
                            <a href="{{route('manageservices')}}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-100 text-gray-600 hover:text-gray-800 border-l-4 border-transparent hover:border-darkPurple pr-6
                           {{ request()->routeIs('manageservices') ? 'bg-purple-300 border-purple-500 rounded-lg text-gray-900 font-semibold' : '' }}
                            ">
                              <span class="inline-flex justify-center items-center ml-4">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                              </span>
                              <span class="ml-2 text-sm tracking-wide truncate">Manage Services</span>
                            </a>
                          </li>

                          <li>
                            <a href="{{ route('managecategories') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent hover:border-darkPurple pr-6
                    {{ request()->routeIs('managecategories') ? 'bg-purple-300 border-purple-500 rounded-lg text-gray-900 font-semibold' : '' }}
                    ">
                      <span class="inline-flex justify-center items-center ml-4">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                      </span>
                                <span class="ml-2 text-sm tracking-wide truncate">Manage Categories</span>
                            </a>
                        </li>

                            @if(Auth::user()->role_id == 1 )

                            <li>
                                <a href="{{ route('manageemployees') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent hover:border-darkPurple pr-6
                       {{ request()->routeIs('manageemployees') ? 'bg-purple-300 border-purple-500 rounded-lg text-gray-900 font-semibold' : '' }}
                        ">
                          <span class="inline-flex justify-center items-center ml-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"> <path stroke-linecap="round" stroke-linejoin="round" d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z"> </path></svg>
                          </span>
                                    <span class="ml-2 text-sm tracking-wide truncate">Manage Employees</span>
                                </a>
                            </li>


                            <li>
                                <a href="{{route('managejobcategories')}}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent hover:border-darkPurple pr-6
                                {{ request()->routeIs('managejobcategories') ? 'bg-purple-300 border-purple-500 rounded-lg text-gray-900 font-semibold' : '' }}
                                ">
                                  <span class="inline-flex justify-center items-center ml-4">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                                  </span>
                                  <span class="ml-2 text-sm tracking-wide truncate">Manage Job Categories</span>
                                </a>
                              </li>


                            @endif


                          <li class="px-5">
                            <div class="flex flex-row items-center h-8">
                              <div class="text-sm font-light tracking-wide text-gray-500">INVENTORY MANAGING</div>
                            </div>
                          </li>

                          <li>
                            <a href="{{ route('manageequipments') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent hover:border-darkPurple pr-6
                            {{ request()->routeIs('manageequipments') ? 'bg-purple-300 border-purple-500 rounded-lg text-gray-900 font-semibold' : '' }}
                            ">

                            <span class="inline-flex justify-center items-center ml-4">
                              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m7.848 8.25 1.536.887M7.848 8.25a3 3 0 1 1-5.196-3 3 3 0 0 1 5.196 3Zm1.536.887a2.165 2.165 0 0 1 1.083 1.839c.005.351.054.695.14 1.024M9.384 9.137l2.077 1.199M7.848 15.75l1.536-.887m-1.536.887a3 3 0 1 1-5.196 3 3 3 0 0 1 5.196-3Zm1.536-.887a2.165 2.165 0 0 0 1.083-1.838c.005-.352.054-.695.14-1.025m-1.223 2.863 2.077-1.199m0-3.328a4.323 4.323 0 0 1 2.068-1.379l5.325-1.628a4.5 4.5 0 0 1 2.48-.044l.803.215-7.794 4.5m-2.882-1.664A4.33 4.33 0 0 0 10.607 12m3.736 0 7.794 4.5-.802.215a4.5 4.5 0 0 1-2.48-.043l-5.326-1.629a4.324 4.324 0 0 1-2.068-1.379M14.343 12l-2.882 1.664" />
                              </svg>
                            </span>
                            <span class="ml-2 text-sm tracking-wide truncate">Manage Equipment and Supplies</span>
                            </a>
                          </li>

                          <li>
                            <a href="{{ route('managesupplies') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent hover:border-darkPurple pr-6
                           {{ request()->routeIs('managesupplies') ? 'bg-purple-300 border-purple-500 rounded-lg text-gray-900 font-semibold' : '' }}
                            ">

                            <span class="inline-flex justify-center items-center ml-4">
                              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m7.848 8.25 1.536.887M7.848 8.25a3 3 0 1 1-5.196-3 3 3 0 0 1 5.196 3Zm1.536.887a2.165 2.165 0 0 1 1.083 1.839c.005.351.054.695.14 1.024M9.384 9.137l2.077 1.199M7.848 15.75l1.536-.887m-1.536.887a3 3 0 1 1-5.196 3 3 3 0 0 1 5.196-3Zm1.536-.887a2.165 2.165 0 0 0 1.083-1.838c.005-.352.054-.695.14-1.025m-1.223 2.863 2.077-1.199m0-3.328a4.323 4.323 0 0 1 2.068-1.379l5.325-1.628a4.5 4.5 0 0 1 2.48-.044l.803.215-7.794 4.5m-2.882-1.664A4.33 4.33 0 0 0 10.607 12m3.736 0 7.794 4.5-.802.215a4.5 4.5 0 0 1-2.48-.043l-5.326-1.629a4.324 4.324 0 0 1-2.068-1.379M14.343 12l-2.882 1.664" />
                              </svg>
                            </span>
                            <span class="ml-2 text-sm tracking-wide truncate">Manage Consumables</span>
                            </a>
                          </li>


                          <li>
                            <a href="{{ route('manageonlinesuppliers') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent hover:border-darkPurple pr-6
                           {{ request()->routeIs('manageonlinesuppliers') ? 'bg-purple-300 border-purple-500 rounded-lg text-gray-900 font-semibold' : '' }}
                            ">

                            <span class="inline-flex justify-center items-center ml-4">
                              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m7.848 8.25 1.536.887M7.848 8.25a3 3 0 1 1-5.196-3 3 3 0 0 1 5.196 3Zm1.536.887a2.165 2.165 0 0 1 1.083 1.839c.005.351.054.695.14 1.024M9.384 9.137l2.077 1.199M7.848 15.75l1.536-.887m-1.536.887a3 3 0 1 1-5.196 3 3 3 0 0 1 5.196-3Zm1.536-.887a2.165 2.165 0 0 0 1.083-1.838c.005-.352.054-.695.14-1.025m-1.223 2.863 2.077-1.199m0-3.328a4.323 4.323 0 0 1 2.068-1.379l5.325-1.628a4.5 4.5 0 0 1 2.48-.044l.803.215-7.794 4.5m-2.882-1.664A4.33 4.33 0 0 0 10.607 12m3.736 0 7.794 4.5-.802.215a4.5 4.5 0 0 1-2.48-.043l-5.326-1.629a4.324 4.324 0 0 1-2.068-1.379M14.343 12l-2.882 1.664" />
                              </svg>
                            </span>
                            <span class="ml-2 text-sm tracking-wide truncate">Manage Online Suppliers</span>
                            </a>
                          </li>

                          <li class="px-5">
                            <div class="flex flex-row items-center h-8">
                              <div class="text-sm font-light tracking-wide text-gray-500">QR PAYMENT SETTING</div>
                            </div>
                          </li>


                          <li>
                            <a href="{{ route('managepayments') }}" class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent hover:border-darkPurple pr-6
                           {{ request()->routeIs('managepayments') ? 'bg-purple-300 border-purple-500 rounded-lg text-gray-900 font-semibold' : '' }}
                            ">

                            <span class="inline-flex justify-center items-center ml-4">
                            <svg class="w-6 h-6 text-gray-600 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                            <path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M4 4h6v6H4V4Zm10 10h6v6h-6v-6Zm0-10h6v6h-6V4Zm-4 10h.01v.01H10V14Zm0 4h.01v.01H10V18Zm-3 2h.01v.01H7V20Zm0-4h.01v.01H7V16Zm-3 2h.01v.01H4V18Zm0-4h.01v.01H4V14Z"/>
                            <path stroke="currentColor" stroke-linejoin="round" stroke-width="2" d="M7 7h.01v.01H7V7Zm10 10h.01v.01H17V17Z"/>
                          </svg>

                            </span>
                            <span class="ml-2 text-sm tracking-wide truncate">Manage QR Codes</span>
                            </a>
                          </li>


                          <li class="px-5">
                            <div class="flex flex-row items-center h-8">
                              <div class="text-sm font-light tracking-wide text-gray-500">REPORT</div>
                            </div>
                          </li>

                          <li>
                            <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700" aria-controls="dropdown-example" data-collapse-toggle="dropdown-example">
                            <svg class="w-6 h-6 text-gray-600 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.6 16.733c.234.269.548.456.895.534a1.4 1.4 0 0 0 1.75-.762c.172-.615-.446-1.287-1.242-1.481-.796-.194-1.41-.861-1.241-1.481a1.4 1.4 0 0 1 1.75-.762c.343.077.654.26.888.524m-1.358 4.017v.617m0-5.939v.725M4 15v4m3-6v6M6 8.5 10.5 5 14 7.5 18 4m0 0h-3.5M18 4v3m2 8a5 5 0 1 1-10 0 5 5 0 0 1 10 0Z"/>
                              </svg>


                                  <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Sales Report</span>
                                  <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                     <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
                                  </svg>
                            </button>
                            <ul id="dropdown-example" class="hidden py-2 space-y-2">
                                  <li>
                                     <a  href="{{ route('daily.report') }}"  class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Daily</a>
                                  </li>
                                  <li>
                                    <a href="{{ route('weekly.report') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Weekly</a>
                                  </li>
                                  <li>
                                    <a href="{{ route('monthly.report') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Monthly</a>
                                  </li>
                                  <li>
                                    <a href="{{ route('quarterly.report') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Quarterly</a>
                                  </li>
                                  <li>
                                    <a href="{{ route('annual.report') }}" class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg pl-11 group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">Annualy</a>
                                  </li>
                            </ul>
                         </li>

                         

                            @else

                          @endif


                        </ul>
                    </div>
                </aside>

        {{-- <div class="max-w-9xl mx-auto sm:px-6 lg:px-8"> --}}
        <div class="">

            {{-- <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg"> --}}
            @if (session('errormsg'))
                <div class="mb-4 font-medium text-sm text-red-600">
                    {{ session('errormsg') }}
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('success') }}
                </div>
            @endif



                <div>
                    {{ $slot }}
                </div>
            </div>
        </div>
        </div>

   <script>
   document.getElementById('sidebar-toggle').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('hidden');
  });

   </script>


</x-app-layout>
