<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between mb-4">
                        <h1 class="text-2xl font-bold">{{ __('Tambah User') }}</h1>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-500 text-white p-4 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('user.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="block">Nama</label>
                            <input type="text" name="name" id="name" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:border-blue-400" required>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block">Email</label>
                            <input type="email" name="email" id="email" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:border-blue-400" required>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="block">Password</label>
                            <input type="password" name="password" id="password" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:border-blue-400" required>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="block">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:border-blue-400" required>
                        </div>

                        <div class="mb-4">
                            <label for="role" class="block">Role</label>
                            <select name="role" id="role" class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:border-blue-400" required>
                                <option value="">-- Pilih Role --</option>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                                <option value="supervisor">Supervisor</option>
                            </select>
                        </div>

                        <button type="submit" class="bg-blue-500 px-4 py-2 rounded">Simpan</button>
                        <button type="reset" class="bg-gray-500 px-4 py-2 rounded">Batal</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
