<x-app-layout>

    <div class="bg-gray-100 py-8" x-data="{ showCheckoutConfirmation: false }">
        <div class="container mx-auto px-4 md:w-11/12">
            <h1 class="text-2xl font-bold mb-4 text-salonPurple">CART</h1>
            <hr class="my-4 border-gray-400 sm:mx-auto dark:border-gray-700 lg:my-8" />


            @if(session('unavailable_employees'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    Swal.fire({
                        title: 'Unavailable Slots',
                        html: `@foreach(session('unavailable_employees') as $unavailable_employee)
                            <li>{{ $unavailable_employee['date'] }}: {{ $unavailable_employee['time'] }} - {{ $unavailable_employee['first_name'] }}</li>
                        @endforeach`,
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                });
            </script>
            @endif

            <div class="flex flex-col md:flex-row gap-4">
            <div class="md:w-3/4">
    <div class="bg-white rounded-lg shadow-md p-6 mb-4">
        <div class="overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr>
                        <th class="text-left font-semibold py-2 px-4">Image</th>
                        <th class="text-left font-semibold py-2 px-4">Service Name</th>
                        <th class="text-left font-semibold py-2 px-4">Price</th>
                        <th class="text-left font-semibold py-2 px-4">Date</th>
                        <th class="text-left font-semibold py-2 px-4">Time Slot</th>
                        <th class="text-left font-semibold py-2 px-4">Staff Assigned</th>
                        <th class="text-left font-semibold py-2 px-4"></th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($cart->services) && $cart->services->isNotEmpty())
                        @foreach($cart->services as $service)
                            <tr class="border-t">
                                <td class="py-4 px-4">
                                    <div class="flex items-center">
                                        <img class="h-16 w-16 mr-1" src="{{ '/storage/' . $service->image }}" alt="{{ $service->name . ' image'}}">
                                        
                                    </div>
                                </td>
                                <td class="py-4 px-4">{{ $service->name }}</td>
                                <td class="py-4 px-4">₱ {{ number_format($service->price, 0, '.', ',') }}</td>
                                <td class="py-4 px-4">{{ $service->pivot->date }}</td>
                                <td class="py-4 px-4">
                                    {{ date('g:i a', strtotime($service->pivot->time)) }}
                                </td>
                                <td class="py-4 px-4">{{ $service->pivot->first_name }}</td>
                                <form action="{{ route('cart.remove-item', ['cart_service_id' => $service->pivot->id]) }}" method="post">
                                    @csrf
                                    @method('delete')
                                    <td class="py-4 px-4">
                                        <button type="submit" class="text-red-500 hover:text-red-600 font-semibold">Remove</button>
                                    </td>
                                </form>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center py-8 px-4">No items in cart</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

                <div class="md:w-1/4">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold mb-4">Summary</h2>


                        <hr class="my-2 border-gray-400">
                        <div class="flex justify-between mb-2">
                            <span class="font-semibold">Total</span>
                            <span class="font-semibold">₱ {{ number_format($cart?->total, 0, '.', ',') }}</span>
                        </div>
                        <button
                        @click="showCheckoutConfirmation = {{ isset($cart->services) && $cart->services->isNotEmpty() ? 'true' : 'false' }}"
                        :disabled="{{ isset($cart->services) && $cart->services->isNotEmpty() ? 'false' : 'true' }}"
                        class="bg-purple-500 text-white py-2 px-4 rounded-lg mt-4 w-full disabled:opacity-50 disabled:cursor-not-allowed">
                        Confirm
                    </button>                    </div>
                </div>
            </div>
        </div>

        <!-- Checkout Confirmation Modal -->
        <div x-data="{ paymentMethod: '', qrImage: '', error: '' }" x-show="showCheckoutConfirmation" x-cloak class="fixed inset-0 overflow-y-auto z-50 flex items-center justify-center">
            <div class="fixed inset-0 transition-opacity -z-10" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <div class="bg-white rounded-lg p-4 max-w-md mx-auto">
                <h2 class="text-xl font-semibold text-salonPurple">Confirm Appointment</h2>
                <hr class="my-2 border-gray-400">
                <p>Are you sure you want to confirm your appointment?</p>
                <p>Select your payment option:</p>
                <form action="{{ route('cart.checkout') }}" method="post">
                    @csrf
                    <div class="mt-2">
                        <label class="inline-flex items-center">
                            <input type="radio" name="payment_method" value="cash" class="form-radio text-purple-600" x-model="paymentMethod">
                            <span class="ml-2">Cash</span>
                        </label>
                    </div>
                    <div class="mt-2">
                        <label class="inline-flex items-center">
                            <input type="radio" name="payment_method" value="online" class="form-radio text-purple-600" x-model="paymentMethod" @change="fetchQrCode">
                            <span class="ml-2">Online</span>
                        </label>
                    </div>

                    <!-- Display QR Code -->
                    <div class="mt-4" x-show="paymentMethod === 'online'" x-cloak>
                        <template x-if="qrImage">
                            <div class="mt-4">
                                <p>Scan the QR Code to complete the payment:</p>
                                <img :src="qrImage" alt="QR Code" class="h-40 w-40">
                            </div>
                        </template>
                        <p x-show="!qrImage" class="text-gray-500">Loading QR Code...</p>
                        <div class="mt-4">
                            <label for="lastFourDigits" class="block text-sm font-medium text-gray-700">
                                Enter the last four digits of your payment:
                            </label>
                            <input
                                type="text"
                                id="lastFourDigits"
                                name="last_four_digits"
                                x-model="lastFourDigits"
                                maxlength="4"
                                pattern="\d{4}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="1234"
                                :disabled="paymentMethod !== 'online'"
                            />
                        </div>
                        <p class="text-red-500 mt-2">*Make sure to come on time.</p>
                        <p class="text-red-500 mt-2">*Grace Period: 10 minutes</p>
                        <p class="text-red-500">*Please be advised that coming late will result in automatic cancellation.</p>
                    </div>

                    <!-- Error message -->
                    <p x-show="error" class="text-red-500 mt-2" x-text="error"></p>

                    <div class="mt-4 flex justify-end space-x-4">
                        <button type="button" class="px-4 py-2 text-sm font-medium text-gray-700 border border-gray-300 rounded-md hover:bg-gray-50" @click="showCheckoutConfirmation = false">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-purple-600 border border-transparent rounded-md hover:bg-purple-700">
                            Confirm
                        </button>
                    </div>
                </form>
            </div>
        </div>


        <script>
            function fetchQrCode() {
            fetch('{{ route('get.qr.code') }}')
                .then(response => response.json())
                .then(data => {
                    this.qrImage = data.qrImage;
                })
                .catch(error => {
                    console.error('Error fetching QR code:', error);
                });
        }
            </script>

    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $.ajax({
                url: '/checkout',
                type: 'POST',
                data: { /* Your form data */ },
                success: function(response) {
                    // Trigger SweetAlert with the message from the response
                    Swal.fire({
                        title: 'Success!',
                        text: response.success, // This should be your response.success message
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // Redirect or reload the page after confirmation
                        window.location.href = '/dashboard';  // Adjust the URL as needed
                    });
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        });
    </script>
@endif
</x-app-layout>
