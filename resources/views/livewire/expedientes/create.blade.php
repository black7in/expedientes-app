<div class="max-w-3xl">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-1.5 text-xs text-slate-400 mb-6">
        <a href="{{ route('expedientes.index') }}" wire:navigate class="hover:text-slate-700 transition-colors">Expedientes</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-slate-600">Nuevo expediente</span>
    </div>

    <form wire:submit="guardar" class="space-y-5" novalidate>

        {{-- Datos del expediente --}}
        <div class="bg-white border border-slate-200 rounded-lg p-6 space-y-4">
            <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Datos del expediente</h2>

            <div class="grid grid-cols-2 gap-4">
                {{-- Número de expediente --}}
                <div class="col-span-2 sm:col-span-1 space-y-1.5">
                    <label class="text-xs font-medium text-slate-700">
                        Nro. de expediente
                        <span class="text-slate-400 font-normal ml-1">(opcional)</span>
                    </label>
                    <input
                        wire:model="numero_expediente"
                        type="text"
                        placeholder="Ej: 125/2026"
                        class="w-full h-8 px-3 text-xs border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 font-mono"
                    >
                    <p class="text-[11px] text-slate-400">Puede completarse luego cuando el juzgado lo asigne</p>
                </div>

                {{-- Fecha inicio --}}
                <div class="col-span-2 sm:col-span-1 space-y-1.5">
                    <label class="text-xs font-medium text-slate-700">Fecha de inicio <span class="text-red-500">*</span></label>
                    <input
                        wire:model="fecha_inicio"
                        type="date"
                        class="w-full h-8 px-3 text-xs border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400
                               @error('fecha_inicio') border-red-400 @enderror"
                    >
                    @error('fecha_inicio') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Juzgado --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-medium text-slate-700">Juzgado <span class="text-red-500">*</span></label>
                    <select
                        wire:model="juzgado_id"
                        class="w-full h-8 px-3 text-xs border border-slate-200 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400
                               @error('juzgado_id') border-red-400 @enderror"
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
                    <label class="text-xs font-medium text-slate-700">Tipo de proceso <span class="text-red-500">*</span></label>
                    <select
                        wire:model="tipo_proceso_id"
                        class="w-full h-8 px-3 text-xs border border-slate-200 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400
                               @error('tipo_proceso_id') border-red-400 @enderror"
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
                    <label class="text-xs font-medium text-slate-700">Estado inicial</label>
                    <select
                        wire:model="estado"
                        class="w-full h-8 px-3 text-xs border border-slate-200 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10"
                    >
                        <option value="activo">Activo</option>
                        <option value="suspendido">Suspendido</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Partes procesales (PB-14) --}}
        <div class="bg-white border border-slate-200 rounded-lg p-6 space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-semibold text-slate-500 uppercase tracking-wide">Partes procesales</h2>
                @error('partes') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Buscador de persona --}}
            <div class="relative" x-data>
                <label class="text-xs font-medium text-slate-700 block mb-1.5">Agregar parte</label>
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        wire:model.live.debounce.300ms="buscarPersona"
                        type="text"
                        placeholder="Buscar por nombre o CI/NIT..."
                        class="w-full h-8 pl-9 pr-3 text-xs border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400"
                    >
                </div>

                @if($mostrarResultados)
                    <div class="absolute z-20 top-full mt-1 w-full bg-white border border-slate-200 rounded-md shadow-lg overflow-hidden">
                        @forelse($resultadosPersona as $persona)
                            <button type="button"
                                    wire:click="seleccionarPersona('{{ $persona['id'] }}')"
                                    class="w-full text-left px-3 py-2 hover:bg-slate-50 transition-colors flex items-center justify-between gap-2">
                                <span class="text-xs font-medium text-slate-800">{{ $persona['nombre_completo'] }}</span>
                                <span class="text-[11px] text-slate-400 font-mono">{{ $persona['ci_nit'] }}</span>
                            </button>
                        @empty
                            <div class="px-3 py-3 text-xs text-center text-slate-400">
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
                <div class="border border-slate-200 rounded-md divide-y divide-slate-100 overflow-hidden">
                    @foreach($partes as $i => $parte)
                        <div class="flex items-center gap-3 px-3 py-2.5">
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-medium text-slate-800 truncate">{{ $parte['nombre_completo'] }}</p>
                                <p class="text-[11px] text-slate-400 font-mono">{{ $parte['ci_nit'] }}</p>
                            </div>

                            <select
                                wire:model.live="partes.{{ $i }}.rol_procesal"
                                class="h-7 px-2 text-xs border border-slate-200 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10"
                            >
                                <option value="demandante">Demandante</option>
                                <option value="demandado">Demandado</option>
                            </select>

                            <button type="button"
                                    wire:click="marcarCliente({{ $i }})"
                                    title="{{ $parte['es_cliente'] ? 'Cliente del estudio' : 'Marcar como cliente' }}"
                                    class="text-xs px-2 py-1 rounded border transition-colors
                                           {{ $parte['es_cliente']
                                               ? 'bg-blue-50 text-blue-700 border-blue-200 font-medium'
                                               : 'text-slate-400 border-slate-200 hover:border-slate-300' }}">
                                Cliente
                            </button>

                            <button type="button"
                                    wire:click="removerParte({{ $i }})"
                                    class="text-slate-400 hover:text-red-500 transition-colors p-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="border border-dashed border-slate-200 rounded-md py-6 text-center text-xs text-slate-400">
                    Buscá y agregá las partes procesales del expediente
                </div>
            @endif
        </div>

        {{-- Acciones --}}
        <div class="flex items-center gap-2 justify-end">
            <a href="{{ route('expedientes.index') }}" wire:navigate
               class="h-8 px-4 text-xs text-slate-600 border border-slate-200 rounded-md hover:bg-slate-50 transition-colors inline-flex items-center">
                Cancelar
            </a>
            <button type="submit"
                    wire:loading.attr="disabled"
                    class="h-8 px-4 bg-slate-900 text-white text-xs font-medium rounded-md hover:bg-slate-800 transition-colors
                           disabled:opacity-50 inline-flex items-center gap-1.5">
                <span wire:loading.remove>Crear expediente</span>
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
