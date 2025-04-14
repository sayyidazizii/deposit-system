<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Produk') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Kolom Produk --}}
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h1 class="text-2xl font-bold mb-4 dark:text-white">{{ __('Daftar Produk') }}</h1>

                    <div class="flex flex-wrap gap-4">
                        @foreach ($products as $item)
                            <div class="w-full md:w-[48%] bg-gray-50 dark:bg-gray-700 rounded-2xl shadow-md p-4 transition hover:shadow-lg">
                                <h2 class="text-xl text-black dark:text-white font-semibold mb-2">{{ $item->name }}</h2>
                                <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400 mb-1">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-300 mb-4">Stok: {{ $item->stock }}</p>

                                <form action="{{ route('cart.add', $item->id) }}" method="POST" class="flex gap-2 items-center">
                                    @csrf
                                    <input type="number" name="quantity" value="1" min="1" max="{{ $item->stock }}" class="px-4 py-2 border rounded-md dark:bg-gray-600 dark:text-white dark:border-gray-500" required>
                                    <button class="w-full md:w-auto bg-indigo-500 text-white px-3 py-2 rounded-lg hover:bg-indigo-600 text-sm">
                                        Beli
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Kolom Cart --}}
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h2 class="text-xl font-bold mb-4 dark:text-white">Keranjang</h2>

                    @if(session('cart') && count(session('cart')) > 0)
                        <ul class="space-y-4">
                            @php
                                $totalAmount = 0;
                            @endphp
                            @foreach (session('cart') as $id => $item)
                                @php
                                    $totalAmount += $item['price'] * $item['quantity'];
                                @endphp
                                <li class="flex justify-between items-center">
                                    <div>
                                        <p class="font-semibold dark:text-white">{{ $item['name'] }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-300">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-300">Jumlah: {{ $item['quantity'] }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-300">Subtotal: Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                                    </div>
                                    <form action="{{ route('cart.remove', $id) }}" method="POST">
                                        @csrf
                                        <button class="text-red-500 hover:underline">Hapus</button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>

                        {{-- Tampilkan Total Amount --}}
                        <div class="mt-4 text-lg font-semibold dark:text-white">
                            <p>Total: Rp {{ number_format($totalAmount, 0, ',', '.') }}</p>
                        </div>

                        <form action="{{ route('cart.checkout') }}" method="POST" class="mt-6">
                            @csrf
                            <button class="w-full bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Checkout</button>
                        </form>
                    @else
                        <p class="text-gray-400 dark:text-gray-600">Keranjang kosong.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
