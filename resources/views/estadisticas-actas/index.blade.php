<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Estadísticas Actas') }}
        </h2>
    </x-slot>

    <div class="py-2">
        {{-- Más ancho que el max-w-7xl del resto: el listado de actas tiene diez
             columnas y con 1280px la de Estado quedaba fuera de vista. --}}
        <div class="max-w-[1700px] mx-auto sm:px-6 lg:px-8">
            <livewire:estadisticas-actas />
        </div>
    </div>
</x-app-layout>
