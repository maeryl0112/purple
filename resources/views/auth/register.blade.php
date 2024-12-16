<x-app-layout>

    <div class="min-h-screen flex fle-col items-center justify-center py-2 px-4 bg-gradient-to-r from-secondary to-white md:rounded-tr-xl">
        <div class="grid md:grid-cols-2 items-center gap-4 max-w-6xl w-full ">
                    <div class="bg-white border border-gray-300 rounded-lg p-6 max-w-md shadow-[0_2px_22px_-4px_rgba(93,96,127,0.2)] max-md:mx-auto">


                            <div class="mb-4">
                                <h3 class="text-salonPurple text-3xl font-extrabold text-center">REGISTER</h3>
                                <p class="text-gray-500 text-sm mt-4 leading-relaxed text-center">Create new account</p>
                              </div>
        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <x-label for="name" value="{{ __('Full Name') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Enter your full name" />
                @error('name')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="Enter your email" />
            </div>

           <div class="mt-4">
                <x-label for="phone_number" value="{{ __('Phone Number') }}" />
                <input
                    id="phone_number"
                    name="phone_number"
                    type="tel"
                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"
                    required
                    autocomplete="off"
                />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />

                            <div class="ml-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-button class="ml-4">
                    {{ __('Register') }}
                </x-button>
            </div>
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
            document.addEventListener('DOMContentLoaded', function () {
                const phoneInputField = document.querySelector("#phone_number");
                const iti = window.intlTelInput(phoneInputField, {
                    initialCountry: "ph", // Set your default country
                    separateDialCode: true, // Displays the country code separately
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.min.js", // Utility script
                });

                // Optional: Set the full number in a hidden input for the backend
                const phoneInputHidden = document.createElement("input");
                phoneInputHidden.type = "hidden";
                phoneInputHidden.name = "phone_number";
                phoneInputField.form.appendChild(phoneInputHidden);

                // Update the hidden input value on blur
                phoneInputField.addEventListener("blur", function () {
                    phoneInputHidden.value = iti.getNumber(); // Gets the full number including country code
                });
            });
        </script>
</x-app-layout>
