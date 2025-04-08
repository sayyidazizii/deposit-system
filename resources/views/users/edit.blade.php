<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit User') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between mb-4">
                        <h1 class="text-2xl font-bold">{{ __('Edit User') }}</h1>
                    </div>

                    @if (session('success'))
                        <div class="bg-green-500 text-white p-4 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-green-500 text-white p-4 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('user.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name" class="block">Nama</label>
                            <input type="hidden" name="id" id="id"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm"
                                value="{{ old('name', $user->id) }}" required>
                            <input type="text" name="name" id="name"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm"
                                value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block">Email</label>
                            <input type="email" name="email" id="email"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm"
                                value="{{ old('email', $user->email) }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="role" class="block">Role</label>
                            <select name="role" id="role"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm" required>
                                <option value="">Pilih Role</option>
                                <option value="admin" {{ $user->hasRole('admin') ? 'selected' : '' }}>Admin</option>
                                <option value="supervisor" {{ $user->hasRole('supervisor') ? 'selected' : '' }}>Supervisor</option>
                                <option value="user" {{ $user->hasRole('user') ? 'selected' : '' }}>User</option>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="password" class="block">Password (kosongkan jika tidak diubah)</label>
                            <input type="password" name="password" id="password"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md shadow-sm"
                                value="{{ old('name', $user->name) }}" required>
                        </div>

                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
                        <a href="{{ route('user.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Batal</a>
                    </form>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
