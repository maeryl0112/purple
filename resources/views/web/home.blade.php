<x-app-layout>
  <x-slot name="mainLogoRoute">
    {{ route('home') }}
  </x-slot>


    <div class="relative">
        {{-- <img class="w-full" src="{{ asset('images\banner-salon.png') }}" alt="Banner image"> --}}
        {{-- <img class="max-h-fit w-full" src="{{ asset('images\salon1.png') }}" alt="Banner image"> --}}
        <!--
  Heads up! 👋

  This component comes with some `rtl` classes. Please remove them if they are not needed in your project.
-->

<section
class="relative bg-cover bg-center bg-no-repeat " style="background-image: url({{ asset('images/background.jpg') }}" ;>
<div class="absolute inset-0 bg-gradient-to-r from-white/90 to-white/0 ltr:bg-gradient-to-r rtl:bg-gradient-to-l sm:bg-transparent sm:from-white/95 sm:to-white/0"></div>

<div
  class="relative mx-auto max-w-screen-xl px-4 py-32 sm:px-6 lg:flex lg:h-screen lg:items-center lg:px-8"
>
  <div class="max-w-xl text-left ltr:sm:text-left rtl:sm:text-right">
    <h1 class="text-3xl font-extrabold sm:text-5xl text-neutral-700">
      Find Your Perfect Salon Experience at
      <strong class="block font-extrabold text-salonPurple">
        Purple Look Hair Salon and Spa.      </strong>
    </h1>

    <p class="mt-4 max-w-lg sm:text-xl/relaxed">
        Purple Look's full-service Salon is prepared in every way to make you look amazingly beautiful.
         </p>

    <div class="mt-8 flex flex-wrap gap-4 text-center">
      <a href="{{route('services')}}" class="block w-full rounded bg-salonPurple px-12 py-3 text-lg font-medium text-white shadow hover:bg-secondary focus:outline-none focus:ring active:bg-purple-500 sm:w-auto">Book Now      </a>

      <a href="{{route('services')}}" class="block w-full rounded bg-white px-12 py-3 text-lg font-medium text-salonPurple shadow hover:text-secondary focus:outline-none focus:ring-offset-purple-400 active:text-purple-500 sm:w-auto">Browse Services</a>


    </div>

  </div>
</div>
</section>

        {{-- <img class="w-full bg-cover" src="{{ asset('images\Salon2.jpg') }}" alt="Banner image"> --}}
        {{-- <div class="absolute right-1 top-5 font-black text-purple-600 text-7xl">30% OFF <br>THIS SEASON</div> --}}
 <div>
    <div class="text-center text-4xl font-semibold text-salonPurple m-2 mt-5">Categories</div>

    <div class="container flex gap-10 p-10 pt-3 justify-center mx-auto">
        @foreach ($categories as $category)
            <a href="#" class="text-center gap-2 duration-300 hover:scale-105">
                @if ($category->image)
                <img class="w-60 rounded-xl" src="{{ asset('storage/'.$category->image) }}" alt="{{ $category->name }}">
                @else
                <img class="w-60 rounded-xl" src="{{ asset('images\nails.jpg') }}" alt="Category Image">
                @endif
                <span class="text-salonPurple text-2xl">{{ $category->name }}</span>
            </a>
        @endforeach
    </div>
 </div>

    </div>

    <section class="pt-5 bg-white">
      <div class="md:w-4/5 mx-auto">
        <div class="mx-auto text-center md:max-w-xl lg:max-w-3xl">
          <h3 class="mb-6 text-3xl text-salonPurple font-bold">Popular Services</h3>
          <p class="mb-6 pb-2 text-gray-700 md:mb-12 md:pb-0">
            Services Popular among our customers.
          </p>
        </div>

        <div class="mb-4 grid gap-4 sm:grid-cols-2 md:mb-8 lg:grid-cols-3 xl:grid-cols-4">

            @if($popularServices->count() > 0)
                @foreach ($popularServices as $service)
                    <x-service-card :service="$service"/>
                @endforeach
            @else
                <p class="mx-auto text-center block text-gray-700 md:mb-12 md:pb-0">
                    No Services Found
                </p>
            @endif
        </div>
      </div>

      <div class="flex justify-end mx-auto pb-5 gap-3 md:w-3/4">

        <a href="{{route('services')}}" class="bg-salonPurple hover:bg-secondary text-white font-bold py-2 px-4 rounded">
          View All Services
        </a>
      </div>


    </section>

    {{--<section class=" w-3/4 p-3 mx-auto pt-5">
    <div>
    <div class="text-center text-4xl font-semibold text-salonPurple m-2">Offers</div>
    </div>
    <div class="flex gap-10 ">
        @if($deals->count() > 0)
            @foreach($deals as $deal)
                @if($deal->is_hidden == false)
                    <div class="max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow ">
                        <a href="#">
                            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900">{{ $deal->name }}</h5>
                        </a>
                        <p class="mb-3 font-normal text-gray-700 ">{{ $deal->description }}</p>
                        <a href="#" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-purple-500 rounded-lg hover:bg-purple-800 focus:ring-4 focus:outline-none focus:ring-purple-300 ">
                            View Offer
                            <svg aria-hidden="true" class="w-4 h-4 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                        </a>
                    </div>
                @endif
            @endforeach
        @else
            <p class="mx-auto text-center block text-gray-700 md:mb-12 md:pb-0">
                No Deals Found
            </p>
        @endif
    </div>
    </section>

    {{-- Gallery --}}
    <section class="pt-5 pb-5 px-10">
        <div class="mx-auto text-center md:max-w-xl lg:max-w-3xl">
          <h3 class="text-3xl text-salonPurple font-bold pb-5">Gallery</h3>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="grid gap-4">
                <div>
                    <img class="h-auto max-w-full rounded-lg" src="{{ asset('images/g1.jpg') }}" alt="">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg" src="{{ asset('images/g2.jpg') }}" alt="">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg" src="{{ asset('images/g3.jpg') }}" alt="">
                </div>
            </div>
            <div class="grid gap-4">
                <div>
                    <img class="h-auto max-w-full rounded-lg" src="{{ asset('images/g4.jpg') }}" alt="">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg" src="{{ asset('images/g5.jpg') }}" alt="">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg" src="{{ asset('images/g6.jpg') }}" alt="">
                </div>
            </div>
            <div class="grid gap-4">
                <div>
                    <img class="h-auto max-w-full rounded-lg" src="{{ asset('images/g7.jpg') }}" alt="">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg" src="{{ asset('images/g8.jpg') }}" alt="">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg" src="{{ asset('images/g9.jpg') }}" alt="">
                </div>
            </div>
            <div class="grid gap-4">
                <div>
                    <img class="h-auto max-w-full rounded-lg" src="{{ asset('images/g10.jpg') }}" alt="">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg" src="{{ asset('images/g11.jpg') }}" alt="">
                </div>
                <div>
                    <img class="h-auto max-w-full rounded-lg" src="{{ asset('images/g12.jpg') }}" alt="">
                </div>
            </div>
        </div>
    </section>

    {{-- Testimonials --}}
    <div class="bg-white pt-2">
          <div class="mx-auto text-center md:max-w-xl lg:max-w-3xl">
            <h3 class="mb-6 text-3xl text-salonPurple font-bold">BRANCHES</h3>
            <p class="mb-6 pb-2 text-gray-700 md:mb-12 md:pb-0">
              Come visit our branch near you.
            </p>
          </div>



<div id="default-carousel" class="relative w-full" data-carousel="slide">
    <!-- Carousel wrapper -->
    <div class="relative h-56 overflow-hidden rounded-lg md:h-96">
         <!-- Item 1 -->
        <div class="hidden duration-700 ease-in-out" data-carousel-item>
            <img src="{{asset('images/b1.png')}}" class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2" alt="...">
        </div>
        <!-- Item 2 -->
        <div class="hidden duration-700 ease-in-out" data-carousel-item>
            <img src="{{asset('images/b2.png')}}" class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2" alt="...">
        </div>
        <!-- Item 3 -->
        <div class="hidden duration-700 ease-in-out" data-carousel-item>
            <img src="{{asset('images/b3.png')}}" class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2" alt="...">
        </div>
        <!-- Item 4 -->
        <div class="hidden duration-700 ease-in-out" data-carousel-item>
            <img src="{{asset('images/b4.png')}}" class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2" alt="...">
        </div>
        <!-- Item 5 -->
        <div class="hidden duration-700 ease-in-out" data-carousel-item>
            <img src="{{asset('images/b5.png')}}" class="absolute block w-full -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2" alt="...">
        </div>
    </div>
    <!-- Slider indicators -->
    <div class="absolute z-30 flex -translate-x-1/2 bottom-5 left-1/2 space-x-3 rtl:space-x-reverse">
        <button type="button" class="w-3 h-3 rounded-full" aria-current="true" aria-label="Slide 1" data-carousel-slide-to="0"></button>
        <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 2" data-carousel-slide-to="1"></button>
        <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 3" data-carousel-slide-to="2"></button>
        <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 4" data-carousel-slide-to="3"></button>
        <button type="button" class="w-3 h-3 rounded-full" aria-current="false" aria-label="Slide 5" data-carousel-slide-to="4"></button>
    </div>
    <!-- Slider controls -->
    <button type="button" class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-prev>
        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
            <svg class="w-4 h-4 text-white dark:text-gray-800 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4"/>
            </svg>
            <span class="sr-only">Previous</span>
        </span>
    </button>
    <button type="button" class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-next>
        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
            <svg class="w-4 h-4 text-white dark:text-gray-800 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
            </svg>
            <span class="sr-only">Next</span>
        </span>
    </button>
</div>

</div>



    <!-- Footer container -->
<footer class="bg-salonPurple text-center p-4 text-neutral-100 lg:text-left">
    <div class="mx-auto max-w-screen-xl">
        <div class="md:flex md:justify-between">
            <div class="mb-6 md:mb-0">
                <h6 class="mb-4 flex items-center justify-center font-semibold text-xl md:justify-start">
                <img class="w-20 h-20" src="{{ asset('images/white-logo.png')}}" alt="">
                 Purple Look Hair Salon and Spa
                </h6>
                <p class="mb-4 flex items-center justify-center md:justify-start">
                    <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    class="mr-3 h-5 w-5">
                    <path
                        d="M11.47 3.84a.75.75 0 011.06 0l8.69 8.69a.75.75 0 101.06-1.06l-8.689-8.69a2.25 2.25 0 00-3.182 0l-8.69 8.69a.75.75 0 001.061 1.06l8.69-8.69z" />
                    <path
                        d="M12 5.432l8.159 8.159c.03.03.06.058.091.086v6.198c0 1.035-.84 1.875-1.875 1.875H15a.75.75 0 01-.75-.75v-4.5a.75.75 0 00-.75-.75h-3a.75.75 0 00-.75.75V21a.75.75 0 01-.75.75H5.625a1.875 1.875 0 01-1.875-1.875v-6.198a2.29 2.29 0 00.091-.086L12 5.43z" />
                    </svg>
                    Stall 2 & 19, 678 Terminal Bayanan Bacoor Cavite
                </p>
                <p class="mb-4 flex items-center justify-center md:justify-start">
                    <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    class="mr-3 h-5 w-5">
                    <path
                        d="M1.5 8.67v8.58a3 3 0 003 3h15a3 3 0 003-3V8.67l-8.928 5.493a3 3 0 01-3.144 0L1.5 8.67z" />
                    <path
                        d="M22.5 6.908V6.75a3 3 0 00-3-3h-15a3 3 0 00-3 3v.158l9.714 5.978a1.5 1.5 0 001.572 0L22.5 6.908z" />
                    </svg>
                    purplelookhairsalonandspa@gmail.com
                </p>
                <p class="mb-4 flex items-center justify-center md:justify-start">
                    <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    class="mr-3 h-5 w-5">
                    <path
                        fill-rule="evenodd"
                        d="M1.5 4.5a3 3 0 013-3h1.372c.86 0 1.61.586 1.819 1.42l1.105 4.423a1.875 1.875 0 01-.694 1.955l-1.293.97c-.135.101-.164.249-.126.352a11.285 11.285 0 006.697 6.697c.103.038.25.009.352-.126l.97-1.293a1.875 1.875 0 011.955-.694l4.423 1.105c.834.209 1.42.959 1.42 1.82V19.5a3 3 0 01-3 3h-2.25C8.552 22.5 1.5 15.448 1.5 6.75V4.5z"
                        clip-rule="evenodd" />
                    </svg>
                    011 554 5521
                </p>
            </div>

    <!-- Services section -->
    <div class="grid pt-5 grid-cols-2 gap-8 sm:gap-6 sm:grid-cols-3">
        <div>
            <h2 class="mb-6 text-sm font-semibold text-white uppercase dark:text-white">Quick Links</h2>
            <ul class="text-white dark:text-gray-400 font-medium">
                <li class="mb-4">
                    <a href="{{route('home')}}" class="hover:underline ">Home</a>
                </li>
                <li class="mb-4">
                    <a href="{{route('about')}}" class="hover:underline ">About</a>
                </li>
                <li class="mb-4">
                    <a href="{{route('services')}}" class="hover:underline ">Services</a>
                </li>
            </ul>
        </div>
        <div>
            <h2 class="mb-6 text-sm font-semibold text-white uppercase dark:text-white">Services</h2>
            <ul class="text-white dark:text-gray-400 font-medium">
              @foreach ($categories as $category)
                <li class="mb-4">
                    <p class="hover:underline">{{ $category->name }}</p>
                </li>
              @endforeach
            </ul>
        </div>
        <div>
            <h2 class="mb-4 text-sm font-semibold text-white uppercase dark:text-white">Branches</h2>
            <ul class="text-white dark:text-gray-400 font-medium">
                <li class="mb-4">
                    <p class="hover:underline ">Sub Branch</p>
                </li>
                <li class="mb-4">
                    <p class="hover:underline">Queen's Row Branch</p>
                </li>
                <li class="mb-4">
                    <p class="hover:underline">Molino IV Branch</p>
                </li>
                <li class="mb-4">
                    <p class="hover:underline">Camella Springville Branch</p>
                </li>
            </ul>
        </div>

    </div>
</div>

<!--Copyright section-->
    <hr class="my-2 border-gray-200 sm:mx-auto dark:border-gray-700 lg:my-8" />
    <div class="sm:flex sm:items-center sm:justify-between">
        <span class="text-sm text-white sm:text-center dark:text-gray-400">© 2024 <a href="{{route('home')}}" class="hover:underline">Purple Look Hair Salon and Spa</a>. All Rights Reserved.
        </span>
        <div class="flex mt-4 space-x-6 sm:justify-center sm:mt-0">
            <a href="https://www.facebook.com/profile.php?id=61569971274984" class="text-white hover:text-gray-900 dark:hover:text-white">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" /></svg>
            </a>
            <a href="#" class="text-white hover:text-gray-900 dark:hover:text-white">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" /></svg>
            </a>
        </div>
    </div>
</footer>
<script>
  // hide offer-banner when user clicks on close
  document.getElementById("offer-banner-close").addEventListener("click", function() {
    document.getElementById("offer-banner").style.display = "none";
  });

</script>

</x-app-layout>
