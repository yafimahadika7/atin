<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Dashboard Dosen
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-lg font-bold mb-2">Selamat Datang, Dosen</h3>
                <p>
                    Login sebagai <b>{{ auth()->user()->name }}</b>
                </p>
                <p class="text-sm text-gray-500 mt-1">
                    Role: <b>DOSEN</b>
                </p>
            </div>
        </div>
    </div>
</x-app-layout>
