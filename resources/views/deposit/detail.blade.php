@php
    use Illuminate\Support\Facades\Auth;

    $user = Auth::user();
@endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Deposit Status') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="container mt-5">
                        @if($deposit->status == 'paid')
                            <div class="alert alert-success">
                                <h4 class="alert-heading">Pembayaran Berhasil!</h4>
                            </div>
                        @elseif($deposit->status == 'pending')
                            <div class="alert alert-warning">
                                <h4 class="alert-heading">Pembayaran Pending</h4>
                            </div>
                        @elseif($deposit->status == 'failed')
                            <div class="alert alert-danger">
                                <h4 class="alert-heading">Pembayaran Gagal</h4>
                            </div>
                        @endif

                        @if (!empty($deposit))
                            <div class="mt-4">
                                <h5>Detail Transaksi:</h5>
                                <ul>
                                    <li><strong>Nomor Transaksi:</strong> {{ $deposit['id'] }}</li>
                                    <li><strong>Nomor VA:</strong> {{ $deposit['vaNumber'] }}</li>
                                    <li><strong>Link Pembayaran:</strong> {{ $deposit['payment_url'] }}</li>
                                    <li><strong>Jumlah Pembayaran:</strong> Rp {{ number_format($deposit['amount'], 0, ',', '.') }}</li>
                                    <li><strong>Cashback:</strong> Rp {{ number_format($deposit['cashback'], 0, ',', '.') }}</li>
                                    <li><strong>Referensi Pembayaran:</strong> {{ $deposit['paymentReference'] }}</li>
                                    <li><strong>Waktu Transaksi:</strong> {{ $deposit['transactionTime'] }}</li>
                                    <li><strong>Status Pembayaran:</strong> {{ ucfirst($deposit['paymentStatus']) }}</li>
                                </ul>
                            </div>
                        @endif

                        @if($user->hasRole('user'))

                        <div class="mt-4">
                            <a href="{{ url('/deposit/history') }}" class="bg-blue-500 text-white px-4 py-2 rounded-md text-xs">Kembali</a>
                        </div>
                        @else
                            <div class="mt-4">
                                <a href="{{ url('/deposit') }}" class="bg-blue-500 text-white px-4 py-2 rounded-md text-xs">Kembali</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

