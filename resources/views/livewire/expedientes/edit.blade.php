<div class="max-w-3xl">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-1.5 text-xs mb-6" style="color: var(--color-muted)">
        <a href="{{ route('expedientes.index') }}" wire:navigate class="hover:text-slate-700 transition-colors">Expedientes</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('expedientes.show', $expediente) }}" wire:navigate class="hover:text-slate-700 transition-colors font-mono">
            {{ $expediente->numero_expediente ?? 'Sin número' }}
        </a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span style="color: var(--color-text-secondary)">Editar</span>
    </div>

    <form wire:submit="guardar" class="space-y-5" novalidate>

        {{-- Datos del expediente --}}
        <div class="bg-white rounded-lg p-6 space-y-4" style="border: 1px solid var(--color-border)">
            <h2 class="text-xs font-semibold uppercase tracking-wide" style="color: var(--color-muted)">Datos del expediente</h2>

            <div class="grid grid-cols-2 gap-4">
                {{-- Número de expediente --}}
                <div class="col-span-2 sm:col-span-1 space-y-1.5">
                    <label class="text-xs font-medium" style="color: var(--color-text-secondary)">
                        Nro. de expediente
                        <span class="font-normal ml-1" style="color: var(--color-subtle)">(opcional)</span>
                    </label>
                    <input
                        wire:model="numero_expediente"
                        type="text"
                        placeholder="Ej: 125/2026"
                        class="w-full h-8 px-3 text-xs rounded-md focus:outline-none focus:ring-2 focus:ring-offset-0 font-mono"
                        style="border: 1px solid var(--color-border)"
                    >
                    <p class="text-[11px]" style="color: var(--color-subtle)">Puede dejarse vacío si aún no fue asignado</p>
                </div>

                {{-- Fecha inicio --}}
                <div class="col-span-2 sm:col-span-1 space-y-1.5">
                    <label class="text-xs font-medium" style="color: var(--color-text-secondary)">
                        Fecha de inicio <span class="text-red-500">*</span>
                    </label>
                    <input
                        wire:model="fecha_inicio"
                        type="date"
                        class="w-full h-8 px-3 text-xs rounded-md focus:outline-none focus:ring-2 focus:ring-offset-0
                               @error('fecha_inicio') border-red-400 @enderror"
                        style="border: 1px solid var(--color-border)"
                    >
                    @error('fecha_inicio') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Juzgado --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-medium" style="color: var(--color-text-secondary)">
                        Juzgado <span class="text-red-500">*</span>
                    </label>
                    <select
                        wire:model="juzgado_id"
                        class="w-full h-8 px-3 text-xs rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-offset-0
                               @error('juzgado_id') border-red-400 @enderror"
                        style="border: 1px solid var(--color-border)"
                    >
                        <option value="">Seleccionar juzgado...</option>
                        @foreach($juzgados as $juzgado)
                            <option value="{{ $juzgado->id }}">{{ $juzgado->nombre }}</option>
                        @endforeach
                    </select>
                    @error('juzgado_id') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Tipo de proceso --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-medium" style="color: var(--color-text-secondary)">
                        Tipo de proceso <span class="text-red-500">*</span>
                    </label>
                    <select
                        wire:model="tipo_proceso_id"
                        class="w-full h-8 px-3 text-xs rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-offset-0
                               @error('tipo_proceso_id') border-red-400 @enderror"
                        style="border: 1px solid var(--color-border)"
                    >
                        <option value="">Seleccionar tipo...</option>
                        @foreach($tiposProceso as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                    @error('tipo_proceso_id') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Estado --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-medium" style="color: var(--color-text-secondary)">Estado</label>
                    <select
                        wire:model="estado"
                        class="w-full h-8 px-3 text-xs rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-offset-0"
                        style="border: 1px solid var(--color-border)"
                    >
                        <option value="activo">Activo</option>
                        <option value="suspendido">Suspendido</option>
                        <option value="en_apelacion">En apelación</option>
                        <option value="archivado">Archivado</option>
                        <option value="concluido">Concluido</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Partes procesales --}}
        <div class="bg-white rounded-lg p-6 space-y-4" style="border: 1px solid var(--color-border)">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-semibold uppercase tracking-wide" style="color: var(--color-muted)">Partes procesales</h2>
                @error('partes') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Buscador de persona --}}
            <div class="relative">
                <label class="text-xs font-medium block mb-1.5" style="color: var(--color-text-secondary)">Agregar parte</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 pointer-events-none"
                         style="color: var(--color-subtle)"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        wire:model.live.debounce.300ms="buscarPersona"
                        type="text"
                        placeholder="Buscar por nombre o CI/NIT..."
                        class="w-full h-8 pl-9 pr-3 text-xs rounded-md focus:outline-none focus:ring-2 focus:ring-offset-0"
                        style="border: 1px solid var(--color-border)"
                    >
                </div>

                @if($mostrarResultados)
                    <div class="absolute z-20 top-full mt-1 w-full bg-white rounded-md shadow-lg overflow-hidden"
                         style="border: 1px solid var(--color-border)">
                        @forelse($resultadosPersona as $persona)
                            <button type="button"
                                    wire:click="seleccionarPersona('{{ $persona['id'] }}')"
                                    class="w-full text-left px-3 py-2 hover:bg-slate-50 transition-colors flex items-center justify-between gap-2">
                                <span class="text-xs font-medium" style="color: var(--color-text)">{{ $persona['nombre_completo'] }}</span>
                                <span class="text-[11px] font-mono" style="color: var(--color-muted)">{{ $persona['ci_nit'] }}</span>
                            </button>
                        @empty
                            <div class="px-3 py-3 text-xs text-center" style="color: var(--color-subtle)">
                                Sin resultados —
                                <a href="{{ route('personas.create') }}" wire:navigate
                                   class="font-medium hover:underline" style="color: var(--color-primary)">
                                    Registrar nueva persona
                                </a>
                            </div>
                        @endforelse
                    </div>
                @endif
            </div>

            {{-- Lista de partes --}}
            @if(count($partes) > 0)
                <div class="rounded-md divide-y overflow-hidden" style="border: 1px solid var(--color-border); border-color: var(--color-border)">
                    @foreach($partes as $i => $parte)
                        <div class="flex items-center gap-3 px-3 py-2.5">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium truncate" style="color: var(--color-text)">{{ $parte['nombre_completo'] }}</p>
                                <p class="text-[11px] font-mono" style="color: var(--color-muted)">{{ $parte['ci_nit'] }}</p>
                            </div>

                            <select
                                wire:model.live="partes.{{ $i }}.rol_procesal"
                                class="h-7 px-2 text-xs rounded-md bg-white focus:outline-none"
                                style="border: 1px solid var(--color-border)"
                            >
                                <option value="demandante">Demandante</option>
                                <option value="demandado">Demandado</option>
                            </select>

                            <button type="button"
                                    wire:click="marcarCliente({{ $i }})"
                                    title="{{ $parte['es_cliente'] ? 'Cliente del estudio' : 'Marcar como cliente' }}"
                                    class="text-xs px-2 py-1 rounded border transition-colors
                                           {{ $parte['es_cliente']
                                               ? 'bg-indigo-50 text-indigo-700 border-indigo-200 font-medium'
                                               : 'border-slate-200 hover:border-slate-300' }}"
                                    style="{{ !$parte['es_cliente'] ? 'color: var(--color-muted)' : '' }}">
                                Cliente
                            </button>

                            <button type="button"
                                    wire:click="removerParte({{ $i }})"
                                    class="p-1 text-slate-400 hover:text-red-500 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-md py-6 text-center text-xs" style="border: 2px dashed var(--color-border); color: var(--color-subtle)">
                    Buscá y agregá las partes procesales del expediente
                </div>
            @endif
        </div>

        {{-- Acciones --}}
        <div class="flex items-center gap-2 justify-end">
            <a href="{{ route('expedientes.show', $expediente) }}" wire:navigate
               class="h-8 px-4 text-xs rounded-md inline-flex items-center transition-colors"
               style="border: 1px solid var(--color-border); color: var(--color-text-secondary)">
                Cancelar
            </a>
            <button type="submit"
                    wire:loading.attr="disabled"
                    class="h-8 px-4 text-white text-xs font-medium rounded-md transition-opacity hover:opacity-90 disabled:opacity-50 inline-flex items-center gap-1.5"
                    style="background-color: var(--color-primary)">
                <span wire:loading.remove>Guardar cambios</span>
                <span wire:loading class="flex items-center gap-1.5">
                    <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Guardando...
                </span>
            </button>
        </div>
    </form>
</div>
