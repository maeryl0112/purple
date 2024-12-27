<x-app-layout>



<!-- Display error message for login failure -->


<div class="min-h-screen flex fle-col items-center justify-center py-2 px-4 bg-gradient-to-r from-secondary to-white md:rounded-tr-xl">
    <div class="grid md:grid-cols-2 items-center gap-4 max-w-6xl w-full ">
                <div class="bg-white border border-gray-300 rounded-lg p-6 max-w-md shadow-[0_2px_22px_-4px_rgba(93,96,127,0.2)] max-md:mx-auto">


                        <div class="mb-8">
                            <h3 class="text-salonPurple text-3xl font-extrabold text-center">SIGN IN</h3>
                            <p class="text-gray-500 text-sm mt-4 leading-relaxed text-center">Sign in to your account and book a appointment.</p>
                          </div>
                          <x-validation-errors class="mb-4" />

                          <!-- Display session status (success message) -->
                          @if (session('status'))
                              <div class="mb-4 font-medium text-sm text-green-600">
                                  {{ session('status') }}
                              </div>
                          @endif

                          <!-- Display error message for login failure -->
                          @if (session('errormsg'))
                              <div class="mb-4 font-medium text-sm text-red-600">
                                  {{ session('errormsg') }}
                              </div>
                          @endif
                          <form class="space-y-4" method="POST" action="{{ route('login') }}">
                            @csrf
                    <div>
                      <x-label class="text-gray-800 text-sm mb-2 block" for="email" value="{{ __('Email') }}"/>
                      <div class="relative flex items-center">
                        <x-input id="email" name="email" :value="old('email')" required autofocus autocomplete="username"  class="w-full text-sm text-gray-800 border border-gray-300 px-4 py-3 rounded-lg outline-purple-600" placeholder="Enter your email" />

                      </div>
                    </div>
                    <div>
                      <x-label class="text-gray-800 text-sm mb-2 block" for="password" value="{{ __('Password') }}"/>
                      <div class="relative flex items-center">
                        <x-input id="password" class="w-full text-sm text-gray-800 border border-gray-300 px-4 py-3 rounded-lg outline-purple-600" type="password" name="password" required autocomplete="current-password" placeholder="Enter your password"/>
                        <button type="button" onclick="togglePasswordVisibility()" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 focus:outline-none">
                    <svg id="show-icon"  class="w-6 h-6 hidden text-gray-500 dark:text-white" 
            aria-hidden="true" 
            xmlns="http://www.w3.org/2000/svg" 
            fill="none" 
            viewBox="0 0 24 24"
        >
            <path stroke="currentColor" stroke-width="2" d="M21 12c0 1.2-4.03 6-9 6s-9-4.8-9-6c0-1.2 4.03-6 9-6s9 4.8 9 6Z"/>
            <path stroke="currentColor" stroke-width="2" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                    </svg>
                    <svg id="hide-icon"  class="w-6 h-6 text-gray-500 dark:text-white" 
            aria-hidden="true" 
            xmlns="http://www.w3.org/2000/svg" 
            fill="none" 
            viewBox="0 0 24 24"
        >
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.933 13.909A4.357 4.357 0 0 1 3 12c0-1 4-6 9-6m7.6 3.8A5.068 5.068 0 0 1 21 12c0 1-3 6-9 6-.314 0-.62-.014-.918-.04M5 19 19 5m-4 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
        </svg>
                </button>
                      </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-4">
                      <div class="flex items-center">
                        <input id="remember-me" name="remember-me" type="checkbox" class="h-4 w-4 shrink-0 text-purple-600 focus:ring-purple-500 border-gray-300 rounded" />
                        <label for="remember-me" class="ml-3 block text-sm text-gray-800">
                          Remember me
                        </label>
                      </div>

                      <div class="text-sm">
                        @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                      </div>
                    </div>

                    <div class="!mt-8">
                      <button class="w-full shadow-xl py-3 px-4 text-sm tracking-wide rounded-lg text-white bg-salonPurple hover:bg-darkPurple focus:outline-none">
                        {{ __('Log in') }}
                      </button>
                    </div>

                    <p class="text-sm !mt-8 text-center text-gray-800">Don't have an account? <a href="{{ route('register') }}" class="text-purple-600 font-semibold hover:underline ml-1 whitespace-nowrap">Register here</a></p>
                  </form>
                </div>
                <div class="lg:h-[400px] md:h-[300px] max-md:mt-8">
                    <img src="{{asset('images/banner2.png')}}" class="lg:w-[100%] w-full h-52 object-contain block mx-auto" alt="login-image" />
                    <h1 class="text-purple-950 italic text-3xl text-center">“Unleash your inner radiance at Purple Look, a full-service salon dedicated to making you feel and look your absolute best. We're a salon for everyone, where beauty knows no bounds.“</h1>
                </div>
    </div>
</div>

<footer class="bg-salonPurple text-center p-2 text-neutral-100 lg:text-left">
    <div class="w-full max-w-screen-xl mx-auto p-2 md:py-4">
        <div class="sm:flex sm:items-center sm:justify-between">
            <a href="https://flowbite.com/" class="flex items-center mb-4 sm:mb-0 space-x-3 rtl:space-x-reverse">
                <img class="w-20 h-20" src="{{ asset('images/white-logo.png')}}" alt="">
                <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">Purple Look Hair Salon and Spa</span>
            </a>
            <ul class="flex flex-wrap items-center mb-6 text-sm font-medium text-white sm:mb-0 dark:text-gray-400">
                <li>
                    <a href="{{route('home')}}" class="hover:underline me-4 md:me-6">Home</a>
                </li>
                <li>
                    <a href="{{route('about')}}" class="hover:underline me-4 md:me-6">About</a>
                </li>
                <li>
                    <a href="{{route('services')}}" class="hover:underline me-4 md:me-6">Services</a>
                </li>

            </ul>
        </div>
        <hr class="my-4 border-gray-200 sm:mx-auto dark:border-gray-700 lg:my-4" />
        <span class="block text-sm text-white sm:text-center dark:text-gray-400">© 2024 <a href="https://flowbite.com/" class="hover:underline">Purple Look Hair Salon and Spa</a>. All Rights Reserved.</span>
    </div>
</footer>

<script>
    function togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const showIcon = document.getElementById('show-icon');
        const hideIcon = document.getElementById('hide-icon');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            showIcon.classList.remove('hidden');
            hideIcon.classList.add('hidden');
        } else {
            passwordInput.type = 'password';
            showIcon.classList.add('hidden');
            hideIcon.classList.remove('hidden');
        }
    }
</script>

</x-app-layout>
