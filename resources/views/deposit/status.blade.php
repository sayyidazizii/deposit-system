

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
                        @if($status == 'success')
                            <div class="alert alert-success">
                                <h4 class="alert-heading">Pembayaran Berhasil!</h4>
                                <p>{{ $message }}</p>
                            </div>
                        @elseif($status == 'pending')
                            <div class="alert alert-warning">
                                <h4 class="alert-heading">Pembayaran Pending</h4>
                                <p>{{ $message }}</p>
                            </div>
                        @elseif($status == 'failed')
                            <div class="alert alert-danger">
                                <h4 class="alert-heading">Pembayaran Gagal</h4>
                                <p>{{ $message }}</p>
                            </div>
                        @elseif($status == 'error')
                            <div class="alert alert-danger">
                                <h4 class="alert-heading">Terjadi Kesalahan</h4>
                                <p>{{ $message }}</p>
                            </div>
                        @endif

                        @if (!empty($transactionDetails))
                            <div class="mt-4">
                                <h5>Detail Transaksi:</h5>
                                <ul>
                                    <li><strong>Nomor Transaksi:</strong> {{ $transactionDetails['merchantOrderId'] }}</li>
                                    <li><strong>Nomor VA:</strong> {{ $transactionDetails['vaNumber'] }}</li>
                                    <li><strong>Link Pembayaran:</strong> {{ $transactionDetails['payment_url'] }}</li>
                                    <li><strong>Jumlah Pembayaran:</strong> Rp {{ number_format($transactionDetails['amount'], 0, ',', '.') }}</li>
                                    <li><strong>Cashback:</strong> Rp {{ number_format($transactionDetails['cashback'], 0, ',', '.') }}</li>
                                    <li><strong>Referensi Pembayaran:</strong> {{ $transactionDetails['paymentReference'] }}</li>
                                    <li><strong>Waktu Transaksi:</strong> {{ $transactionDetails['transactionTime'] }}</li>
                                    <li><strong>Status Pembayaran:</strong> {{ ucfirst($transactionDetails['paymentStatus']) }}</li>
                                </ul>
                            </div>
                        @endif

                        <div class="mt-4">
                            <a href="{{ url('/') }}" class="btn btn-primary">Kembali ke Beranda</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

