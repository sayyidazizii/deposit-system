@php
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Daftar Transaksi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

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

                    <table class="min-w-full table-auto bg-white dark:bg-gray-800 shadow-md rounded-lg">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-700 text-left">
                                <th class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300">ID</th>
                                <th class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300">Nomor</th>
                                <th class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300">Jumlah</th>
                                <th class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300">Status</th>
                                <th class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300">Waktu</th>
                                <th class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $trx)
                                <tr class="border-b dark:border-gray-700">
                                    <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $trx->id }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $trx->transaction_number }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">Rp{{ number_format($trx->amount, 0, ',', '.') }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        @if($trx->status == 'success')
                                            <span class="inline-block bg-green-500 text-white px-2 py-1 text-xs rounded-full">Berhasil</span>
                                        @elseif($trx->status == 'pending')
                                            <span class="inline-block bg-yellow-500 text-white px-2 py-1 text-xs rounded-full">Menunggu</span>
                                        @else
                                            <span class="inline-block bg-red-500 text-white px-2 py-1 text-xs rounded-full">Gagal</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-300">{{ $trx->created_at->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-2 text-sm">
                                        {{-- <a href="{{ route('transactions.show', $trx->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded-md text-xs">Detail</a> --}}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center px-4 py-2 text-gray-500 dark:text-gray-400">Belum ada transaksi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
