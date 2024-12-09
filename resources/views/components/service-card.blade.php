@props([
    /** @var \mixed */
    'service'
])


<div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800 transition-transform duration-300 hover:scale-105 hover:shadow-lg">
    <div class="h-56 w-full">
              <a href="{{route('view-service', ['slug' => $service->slug])}}">
        <img class="mx-auto h-full dark:hidden" src="{{ asset('storage/'. $service->image)}}" alt="" />
      </a>
    </div>
    <div class="pt-6">


      <p class="text-lg font-bold leading-tight text-gray-900 hover:underline dark:text-white">{{ $service->name}}</p>

      <ul class="mt-2 flex items-center gap-4">
        <li class="flex items-center gap-2">
          <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $service->description}}</p>
        </li>

      </ul>

      <div class="mt-4 flex items-center justify-between gap-4">
        <p class="text-2xl font-extrabold leading-tight text-gray-900 dark:text-white">{{ $service->price}}</p>


        <a href="{{route('view-service', ['slug' => $service->slug])}}">
        <button type="button" class="inline-flex items-center rounded-lg bg-salonPurple px-5 py-2.5 text-sm font-medium text-white hover:bg-primary-800 focus:outline-none focus:ring-4  focus:ring-primary-300 dark:bg-primary-600 dark:hover:bg-primary-700 dark:focus:ring-primary-800">
          Book Now
        </button>
        </a>
      </div>
    </div>
  </div>







