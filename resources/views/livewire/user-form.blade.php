<div>
    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
        <form wire:submit="save">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Nombre --}}
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nombre</label>
                    <input type="text" wire:model="name" id="name"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                    @error('name')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                    <input type="email" wire:model="email" id="email"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                    @error('email')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Contraseña --}}
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Contraseña
                        @if ($isEdit)
                            <span class="text-xs text-gray-500">(dejar vacío para no cambiar)</span>
                        @endif
                    </label>
                    <input type="password" wire:model="password" id="password"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                    @error('password')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Confirmar Contraseña --}}
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirmar Contraseña</label>
                    <input type="password" wire:model="password_confirmation" id="password_confirmation"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                </div>

                {{-- Rol --}}
                <div class="md:col-span-2">
                    <label for="rol" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Rol</label>
                    <select wire:model.live="rol" id="rol"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                        @foreach ($roles as $valor => $etiqueta)
                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                    @error('rol')
                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                        @if ($rol === $rolSupervisor)
                            Accede a todo el sistema, incluida la gestión de Operativos, Grupos y Usuarios.
                        @elseif ($rol === $rolVisualizador)
                            Solo consulta: ve únicamente las solapas tildadas y no puede crear, editar ni eliminar nada.
                        @else
                            Carga y edita Hechos, Categorías y Barrios, y ve las Estadísticas.
                        @endif
                    </p>
                </div>

                {{-- Solapas habilitadas (solo para visualizadores) --}}
                @if ($rol === $rolVisualizador)
                    <div class="md:col-span-2">
                        <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">Solapas que puede ver</span>
                        <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2 p-4 rounded-md border border-gray-200 dark:border-gray-700">
                            @foreach ($solapasDisponibles as $slug => $solapa)
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model="solapas" value="{{ $slug }}"
                                        class="rounded border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $solapa['label'] }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('solapas')
                            <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            La gestión de Usuarios no se puede habilitar: es exclusiva de los supervisores.
                        </p>
                    </div>
                @endif
            </div>

            {{-- Botones --}}
            <div class="flex items-center justify-end mt-6 space-x-3">
                <a href="{{ route('usuarios.index') }}" wire:navigate
                   class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">
                    Cancelar
                </a>
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150">
                    {{ $isEdit ? 'Actualizar' : 'Crear' }} Usuario
                </button>
            </div>
        </form>
    </div>
</div>
