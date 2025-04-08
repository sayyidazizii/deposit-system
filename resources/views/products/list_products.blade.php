<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Produk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between mb-4">
                        <h1 class="text-2xl font-bold">{{ __('Daftar Produk') }}</h1>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-500 text-white p-4 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="bg-red-500 text-white p-4 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow starting:opacity-0">
                        <main class="flex max-w-[335px] w-full flex-col-reverse lg:max-w-4xl lg:flex-row flex-wrap gap-4 p-4">
                          <!-- Card Produk -->
                          @foreach ($products as $item)
                          <div class="w-sm lg:w-[calc(20%-0.5rem)] bg-gray-50 rounded-2xl shadow-md p-4 transition hover:shadow-lg">
                            <h2 class="text-xl text-black semibold mb-2"> {{ $item->name }} </h2>
                            <p class="text-lg font-bold text-indigo-600 mb-1">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                            <p class="text-sm text-gray-500 mb-4">Stok: {{ $item->stock }}</p>
                            <button class="w-full bg-indigo-500 text-white px-3 py-2 rounded-lg hover:bg-indigo-600 text-sm">
                              Beli
                            </button>
                          </div>

                          @endforeach

                        </main>
                      </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
