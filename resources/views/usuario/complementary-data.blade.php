<div class="mt-10 sm:mt-0">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <!-- Título e descrição -->
        <div class="md:col-span-1 flex justify-between">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium text-gray-900">Dados Complementares</h3>
                <p class="mt-1 text-sm text-gray-600">
                    Atualize os dados complementares do seu perfil.
                </p>
            </div>
        </div>

        <!-- Formulário -->
        <div class="mt-5 md:mt-0 md:col-span-2">
            <form action="{{ route('usuario.user-data') }}" method="POST">
                @csrf
                <div class="px-4 py-5 bg-white sm:p-6 shadow sm:rounded-md">
                    <!-- CPF -->
                    <div class="col-span-6 sm:col-span-4 mb-4">
                        <label for="cpf" class="block text-sm font-medium text-gray-700">CPF</label>
                        <input type="text" id="cpf" name="cpf" value="{{ old('cpf', $cpf) }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            maxlength="11" required>
                    </div>

                    <!-- Celular -->
                    <div class="col-span-6 sm:col-span-4 mb-4">
                        <label for="celular" class="block text-sm font-medium text-gray-700">Celular</label>
                        <input type="text" id="celular" name="celular" value="{{ old('celular', $celular) }}"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            maxlength="15">
                    </div>
                </div>

                <div
                    class="flex items-center justify-end px-4 py-3 bg-gray-50 text-right sm:px-6 shadow sm:rounded-bl-md sm:rounded-br-md">
                    <button type="button" id="btn-salvar-dados-complementares"
                        class="ml-3 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
