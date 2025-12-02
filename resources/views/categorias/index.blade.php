<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Categorías') }}
            </h2>
            <a href="{{ route('categorias.create') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150">
                Nueva Categoría
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:categorias-index />
        </div>
    </div>
</x-app-layout>
