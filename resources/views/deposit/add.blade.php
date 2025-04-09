<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Deposit') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between mb-4">
                    </div>

                    @if (session('success'))
                        <div class="bg-green-500 text-white p-4 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('deposit.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="amount" class="block">Jumlah</label>
                            <input type="number" name="amount" id="amount" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:border-blue-400" required>
                        </div>

                        <div class="mb-4">
                            <label class="block mb-2 text-lg font-semibold">Pilih Metode Pembayaran</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                @foreach ($paymentMethods as $method)
                                    <label class="block border border-gray-300 dark:border-gray-600 rounded-lg p-4 cursor-pointer hover:border-blue-500 transition-all">
                                        <input type="radio" name="payment_reference" value="{{ $method['paymentMethod'] }}" class="hidden peer" required>
                                        <div class="flex flex-col items-center peer-checked:border-blue-500 peer-checked:ring-2 peer-checked:ring-blue-300 rounded-lg">
                                            <img src="{{ $method['paymentImage'] }}" alt="{{ $method['paymentName'] }}" class="w-16 h-16 object-contain mb-2">
                                            <span class="text-sm text-center dark:text-white">{{ $method['paymentName'] }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>


                        <button type="submit" class="bg-blue-500 px-4 py-2 rounded">Deposit</button>
                        <button type="reset" class="bg-gray-500 px-4 py-2 rounded">Batal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

