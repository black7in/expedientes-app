<div>
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-sm font-semibold" style="color: var(--color-text)">Juzgados</h2>
            <p class="text-xs mt-0.5" style="color: var(--color-muted)">Juzgados y tribunales vinculados a expedientes</p>
        </div>
        <button wire:click="toggleForm" type="button"
                class="inline-flex items-center gap-1.5 h-8 px-3 text-xs font-medium text-white rounded-md hover:opacity-90 transition-opacity"
                style="background-color: {{ $showForm ? '#6b7280' : 'var(--color-primary)' }}">
            @if($showForm)
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Cancelar
            @else
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo juzgado
            @endif
        </button>
    </div>

    {{-- Formulario nuevo --}}
    @if($showForm)
        <div class="bg-white rounded-lg p-4 mb-4" style="border: 1px solid var(--color-border)">
            <form wire:submit="guardar" class="flex items-end gap-3" novalidate>
                <div class="flex-1 space-y-1.5">
                    <label class="text-xs font-medium" style="color: var(--color-text)">Nombre del juzgado <span class="text-red-500">*</span></label>
                    <input wire:model="nombre" type="text"
                           placeholder="Ej: Juzgado Público Civil y Comercial Nº 1"
                           class="w-full h-8 px-3 text-xs rounded-md @error('nombre') border-red-400 @enderror"
                           style="border: 1px solid var(--color-border); color: var(--color-text); outline: none;">
                    @error('nombre') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div class="w-40 space-y-1.5">
                    <label class="text-xs font-medium" style="color: var(--color-text)">Ciudad <span class="text-red-500">*</span></label>
                    <input wire:model="ciudad" type="text"
                           placeholder="Ej: Cochabamba"
                           class="w-full h-8 px-3 text-xs rounded-md @error('ciudad') border-red-400 @enderror"
                           style="border: 1px solid var(--color-border); color: var(--color-text); outline: none;">
                    @error('ciudad') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <button type="submit"
                        wire:loading.attr="disabled"
                        class="h-8 px-4 text-xs font-medium text-white rounded-md hover:opacity-90 transition-opacity flex-shrink-0"
                        style="background-color: var(--color-primary)">
                    Guardar
                </button>
            </form>
        </div>
    @endif

    {{-- Lista --}}
    <div class="bg-white rounded-lg overflow-hidden" style="border: 1px solid var(--color-border)">
        @forelse($juzgados as $juzgado)
            <div class="flex items-center gap-3 px-4 py-3 {{ !$loop->last ? 'border-b' : '' }}"
                 style="{{ !$loop->last ? 'border-color: var(--color-border)' : '' }}">

                @if($editandoId === $juzgado->id)
                    {{-- Edición inline --}}
                    <div class="flex-1 flex items-center gap-2">
                        <input wire:model="editNombre" type="text"
                               class="flex-1 h-7 px-2 text-xs rounded-md"
                               style="border: 1px solid var(--color-primary); outline: none;">
                        <input wire:model="editCiudad" type="text"
                               class="w-32 h-7 px-2 text-xs rounded-md"
                               style="border: 1px solid var(--color-border); outline: none;">
                        <button wire:click="guardarEdicion" type="button"
                                class="h-7 px-3 text-xs font-medium text-white rounded-md"
                                style="background-color: var(--color-primary)">Guardar</button>
                        <button wire:click="cancelarEdicion" type="button"
                                class="h-7 px-3 text-xs rounded-md hover:bg-slate-100"
                                style="color: var(--color-muted)">Cancelar</button>
                    </div>
                @else
                    {{-- Vista normal --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-medium" style="color: var(--color-text)">{{ $juzgado->nombre }}</p>
                        <p class="text-[11px] mt-0.5" style="color: var(--color-muted)">{{ $juzgado->ciudad }}</p>
                    </div>

                    <span class="text-[11px]" style="color: var(--color-subtle)">
                        {{ $juzgado->expedientes()->count() }} expediente(s)
                    </span>

                    <button wire:click="editarJuzgado('{{ $juzgado->id }}')" type="button"
                            class="text-[11px] font-medium hover:underline flex-shrink-0"
                            style="color: var(--color-primary)">Editar</button>

                    <button wire:click="toggleActivo('{{ $juzgado->id }}')" type="button"
                            title="{{ $juzgado->activo ? 'Desactivar' : 'Activar' }}"
                            class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200">
                        <span class="sr-only">Toggle</span>
                        <span class="pointer-events-none inline-block h-4 w-4 rounded-full bg-white shadow transform transition duration-200 {{ $juzgado->activo ? 'translate-x-4' : 'translate-x-0' }}"
                              style="background-color: white; box-shadow: 0 1px 3px rgba(0,0,0,.3)"></span>
                    </button>
                    {{-- fondo del toggle --}}
                    <style>
                        button[wire\:click="toggleActivo('{{ $juzgado->id }}')"] {
                            background-color: {{ $juzgado->activo ? 'var(--color-primary)' : '#d1d5db' }};
                        }
                    </style>
                @endif
            </div>
        @empty
            <div class="px-4 py-10 text-center text-xs" style="color: var(--color-muted)">
                No hay juzgados registrados. Agrega el primero con el botón de arriba.
            </div>
        @endforelse
    </div>
</div>
