<x-app-layout>


<section class="py-24">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
      <div class="grid lg:grid-cols-2 grid-cols-1">
          <div class="lg:mb-0 mb-10">
              <div class="group w-full h-full">
                  <div class="relative h-full">
                      <img src="{{ asset('images/about1.jpg')}}"  alt="ContactUs" class="w-full h-full lg:rounded-l-2xl rounded-2xl bg-blend-multiply bg-salonPurple object-cover"/>
                  </div>
              </div>
          </div>

          <div class="bg-gray-50 p-5 lg:p-11 lg:rounded-r-2xl rounded-2xl">
                @if (session()->has('message'))
                <div class="bg-green-500 text-white p-3 rounded mb-4">
                    {{ session('message') }}
                </div>
                @endif
              <h2 class="text-salonPurple font-manrope text-4xl font-semibold leading-10 mb-11">About Us</h2>
                <p>
                Purple Look's full-service salon is prepared in every way to make you look amazingly beautiful. We are a salon for everyone.
                </p>
                <p></p>
                <p>
                    Purple Look Hair Salon and Spa has been assisting clients to look and feel their very best since 2018. We have a talented team that takes great pride in providing friendly customer service and unique styles that let each client shine.
                </p>
        </div>
    </div>

    <div class="py-20 px-32 flex place-items-center">
        <div class="h-auto rounded  overflow-hidden shadow-lg bg-white">
            <div class="px-4 py-4">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3864.063982042186!2d120.96238687462672!3d14.423475181389621!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397d23f636b6ae3%3A0xa3f77097d8d6d3b9!2sPurple%20Look!5e0!3m2!1sen!2sph!4v1729355238895!5m2!1sen!2sph" width="1000" height="500" style="border:5;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>

</section>






</x-app-layout>
