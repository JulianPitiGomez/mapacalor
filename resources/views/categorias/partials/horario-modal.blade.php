<div id="horarioModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
    <div class="relative mx-auto p-6 border border-gray-200 dark:border-gray-700 w-full max-w-md shadow-2xl rounded-lg bg-white dark:bg-gray-800">
        <div>
            <h3 class="text-xl font-semibold leading-6 text-gray-900 dark:text-gray-100 mb-6" id="horarioModalTitle">
                Agregar Horario
            </h3>
            <form id="horarioForm">
                <input type="hidden" id="horario_id" name="horario_id">

                <div class="mb-4">
                    <label for="horario_nombre" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="horario_nombre" name="nombre" required
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                </div>

                <div class="mb-4">
                    <label for="horario_descripcion" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Descripción
                    </label>
                    <textarea id="horario_descripcion" name="descripcion" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"></textarea>
                </div>

                <div class="mb-6">
                    <label class="inline-flex items-center">
                        <input type="checkbox" id="horario_activo" name="activo" value="1" checked
                            class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Activo</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeHorarioModal()"
                        class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-md hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800 rounded-md hover:bg-gray-700 dark:hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
