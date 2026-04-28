<div>

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-1.5 text-xs mb-5" style="color: var(--color-muted)">
        <a href="{{ route('expedientes.index') }}" wire:navigate
           class="hover:text-slate-700 transition-colors">Expedientes</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-slate-600 font-mono">{{ $expediente->numero_expediente ?? 'Sin número asignado' }}</span>
    </div>

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4 mb-6">
        <div class="flex items-center gap-3">
            <h2 class="text-lg font-semibold text-slate-900 font-mono">
                {{ $expediente->numero_expediente ?? 'Sin número asignado' }}
            </h2>
            <x-estado-badge :estado="$expediente->estado" />
        </div>
        <a href="{{ route('expedientes.edit', $expediente) }}" wire:navigate
           class="inline-flex items-center gap-1.5 h-8 px-3 text-xs font-medium text-white rounded-md transition-opacity hover:opacity-90 flex-shrink-0"
           style="background-color: var(--color-primary)">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Editar
        </a>
    </div>

    {{-- Meta row --}}
    <div class="flex flex-wrap items-center gap-x-5 gap-y-1.5 text-xs mb-7" style="color: var(--color-muted)">
        <span class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/>
            </svg>
            {{ $expediente->juzgado->nombre }}
        </span>
        <span class="text-slate-300">|</span>
        <span class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            {{ $expediente->tipoProceso->nombre }}
        </span>
        <span class="text-slate-300">|</span>
        <span class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            {{ $expediente->abogado->nombre }}
        </span>
        <span class="text-slate-300">|</span>
        <span class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            {{ $expediente->fecha_inicio->format('d/m/Y') }}
        </span>
    </div>

    {{-- Two-column layout --}}
    <div class="flex gap-6 items-start">

        {{-- LEFT — Actuaciones timeline --}}
        <div class="flex-[2] min-w-0">
            <div class="bg-white rounded-lg" style="border: 1px solid var(--color-border)">

                {{-- Section header --}}
                <div class="flex items-center justify-between px-5 py-4"
                     style="border-bottom: 1px solid var(--color-border)">
                    <h3 class="text-xs font-semibold text-slate-700 uppercase tracking-wide">Actuaciones</h3>
                    <button wire:click="toggleActuacionForm"
                            class="inline-flex items-center gap-1.5 h-7 px-3 text-xs font-medium rounded-md transition-colors
                                   {{ $showActuacionForm ? 'bg-slate-100 text-slate-600' : 'text-white' }}"
                            @if(!$showActuacionForm) style="background-color: var(--color-primary)" @endif>
                        @if($showActuacionForm)
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancelar
                        @else
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Agregar actuacion
                        @endif
                    </button>
                </div>

                {{-- Add actuacion form --}}
                @if($showActuacionForm)
                    <div class="px-5 py-4" style="border-bottom: 1px solid var(--color-border)">
                        <form wire:submit="guardarActuacion" class="space-y-3" novalidate>

                            <div class="space-y-1.5">
                                <label class="text-xs font-medium text-slate-700">
                                    Descripcion <span class="text-red-500">*</span>
                                </label>
                                <textarea
                                    wire:model="descripcion"
                                    rows="3"
                                    placeholder="Describa la actuacion procesal..."
                                    class="w-full px-3 py-2 text-xs border border-slate-200 rounded-md resize-none
                                           focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400
                                           @error('descripcion') border-red-400 @enderror"
                                ></textarea>
                                @error('descripcion')
                                    <p class="text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-1.5">
                                    <label class="text-xs font-medium text-slate-700">
                                        Fecha y hora <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        wire:model="fecha"
                                        type="datetime-local"
                                        class="w-full h-8 px-3 text-xs border border-slate-200 rounded-md
                                               focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400
                                               @error('fecha') border-red-400 @enderror"
                                    >
                                    @error('fecha')
                                        <p class="text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="space-y-1.5">
                                    <label class="text-xs font-medium text-slate-700">Tipo</label>
                                    <select
                                        wire:model="tipo_actuacion"
                                        class="w-full h-8 px-3 text-xs border border-slate-200 rounded-md bg-white
                                               focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400
                                               @error('tipo_actuacion') border-red-400 @enderror"
                                    >
                                        <option value="escrito">Escrito</option>
                                        <option value="audiencia">Audiencia</option>
                                        <option value="resolucion">Resolución</option>
                                        <option value="notificacion">Notificación</option>
                                        <option value="recurso">Recurso</option>
                                        <option value="otro">Otro</option>
                                    </select>
                                    @error('tipo_actuacion')
                                        <p class="text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                        wire:loading.attr="disabled"
                                        class="inline-flex items-center gap-1.5 h-8 px-4 text-xs font-medium text-white rounded-md
                                               transition-opacity hover:opacity-90 disabled:opacity-50"
                                        style="background-color: var(--color-primary)">
                                    <span wire:loading.remove wire:target="guardarActuacion">Registrar actuacion</span>
                                    <span wire:loading wire:target="guardarActuacion" class="flex items-center gap-1.5">
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
                @endif

                {{-- Timeline --}}
                <div class="px-5 py-4">
                    @forelse($expediente->actuaciones->sortByDesc('fecha') as $actuacion)
                        @php
                            $dotColor = match($actuacion->tipo_actuacion) {
                                'escrito'      => 'bg-blue-500',
                                'audiencia'    => 'bg-violet-500',
                                'resolucion'   => 'bg-green-500',
                                'notificacion' => 'bg-amber-500',
                                'recurso'      => 'bg-red-500',
                                default        => 'bg-slate-400',
                            };
                            $badgeColor = match($actuacion->tipo_actuacion) {
                                'escrito'      => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                'audiencia'    => 'bg-violet-50 text-violet-700 ring-violet-600/20',
                                'resolucion'   => 'bg-green-50 text-green-700 ring-green-600/20',
                                'notificacion' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                                'recurso'      => 'bg-red-50 text-red-700 ring-red-600/20',
                                default        => 'bg-slate-100 text-slate-600 ring-slate-500/20',
                            };
                            $tipoLabel = match($actuacion->tipo_actuacion) {
                                'escrito'      => 'Escrito',
                                'audiencia'    => 'Audiencia',
                                'resolucion'   => 'Resolución',
                                'notificacion' => 'Notificación',
                                'recurso'      => 'Recurso',
                                default        => 'Otro',
                            };
                        @endphp
                        <div class="flex gap-3 pb-5 last:pb-0 relative">
                            {{-- Left line --}}
                            <div class="flex flex-col items-center flex-shrink-0">
                                <div class="w-2.5 h-2.5 rounded-full mt-0.5 flex-shrink-0 {{ $dotColor }}"></div>
                                @if(!$loop->last)
                                    <div class="w-px flex-1 mt-1.5" style="background-color: var(--color-border)"></div>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0 pb-0.5">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[11px] tabular-nums" style="color: var(--color-muted)">
                                        {{ $actuacion->fecha->format('d/m/Y H:i') }}
                                    </span>
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium ring-1 ring-inset {{ $badgeColor }}">
                                        {{ $tipoLabel }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-800 leading-relaxed">{{ $actuacion->descripcion }}</p>
                                <p class="text-[11px] mt-1" style="color: var(--color-muted)">
                                    {{ $actuacion->usuario->nombre }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center">
                            <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="text-xs text-slate-400">No hay actuaciones registradas</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>

        {{-- RIGHT — Sidebar cards --}}
        <div class="flex-1 min-w-0 space-y-4">

            {{-- Partes procesales --}}
            <div class="bg-white rounded-lg" style="border: 1px solid var(--color-border)">
                <div class="flex items-center justify-between px-4 py-3" style="border-bottom: 1px solid var(--color-border)">
                    <h3 class="text-xs font-semibold text-slate-700 uppercase tracking-wide">Partes procesales</h3>
                    <button wire:click="abrirModalParte" type="button"
                            class="inline-flex items-center gap-1 text-[11px] font-medium hover:opacity-75 transition-opacity"
                            style="color: var(--color-primary)">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Agregar
                    </button>
                </div>
                <div class="px-4 py-3 space-y-3">
                    @forelse($expediente->partes as $parte)
                        @php
                            $rolColor = match($parte->rol_procesal) {
                                'demandante' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                'demandado'  => 'bg-red-50 text-red-700 ring-red-600/20',
                                default      => 'bg-slate-100 text-slate-600 ring-slate-500/20',
                            };
                            $rolLabel = match($parte->rol_procesal) {
                                'demandante' => 'Demandante',
                                'demandado'  => 'Demandado',
                                default      => ucfirst($parte->rol_procesal),
                            };
                        @endphp
                        <div class="flex items-start gap-2">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <p class="text-xs font-medium text-slate-800 truncate">
                                        {{ $parte->persona->nombre_completo }}
                                    </p>
                                    @if($parte->es_cliente)
                                        <span class="inline-flex items-center rounded-full px-1.5 py-0.5 text-[10px] font-medium bg-indigo-50 text-indigo-700 ring-1 ring-inset ring-indigo-600/20">
                                            Cliente
                                        </span>
                                    @endif
                                </div>
                                <p class="text-[11px] font-mono mt-0.5" style="color: var(--color-muted)">
                                    {{ $parte->persona->ci_nit }}
                                </p>
                            </div>
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium ring-1 ring-inset flex-shrink-0 {{ $rolColor }}">
                                {{ $rolLabel }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-center py-3" style="color: var(--color-muted)">Sin partes registradas</p>
                    @endforelse
                </div>
            </div>

            {{-- Documentos --}}
            <div class="bg-white rounded-lg" style="border: 1px solid var(--color-border)">
                <div class="flex items-center justify-between px-4 py-3" style="border-bottom: 1px solid var(--color-border)">
                    <h3 class="text-xs font-semibold text-slate-700 uppercase tracking-wide">Documentos</h3>
                    <a href="{{ route('documentos.upload') }}" wire:navigate
                       class="text-[11px] font-medium transition-colors hover:opacity-75"
                       style="color: var(--color-primary)">
                        Subir documento
                    </a>
                </div>
                <div class="px-4 py-3 space-y-2.5">
                    @forelse($expediente->documentos as $documento)
                        @php
                            $formatoBadge = match(strtoupper($documento->formato ?? '')) {
                                'PDF'  => 'bg-red-50 text-red-700 ring-red-600/20',
                                'DOCX' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                default => 'bg-slate-100 text-slate-600 ring-slate-500/20',
                            };
                            $extraccionBadge = match($documento->estado_extraccion) {
                                'procesado' => 'bg-green-50 text-green-700 ring-green-600/20',
                                'pendiente' => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                                'error'     => 'bg-red-50 text-red-700 ring-red-600/20',
                                default     => 'bg-slate-100 text-slate-600 ring-slate-500/20',
                            };
                            $extraccionLabel = match($documento->estado_extraccion) {
                                'procesado' => 'Procesado',
                                'pendiente' => 'Pendiente',
                                'error'     => 'Error',
                                default     => ucfirst($documento->estado_extraccion ?? ''),
                            };
                        @endphp
                        <div class="flex items-start gap-2">
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('documentos.show', $documento) }}" wire:navigate
                                   class="text-xs font-medium text-slate-800 hover:text-slate-600 transition-colors truncate block">
                                    {{ $documento->nombre_archivo }}
                                </a>
                                <div class="flex items-center gap-1.5 mt-1">
                                    @if($documento->formato)
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium ring-1 ring-inset {{ $formatoBadge }}">
                                            {{ strtoupper($documento->formato) }}
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium ring-1 ring-inset {{ $extraccionBadge }}">
                                        {{ $extraccionLabel }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-6 text-center">
                            <svg class="w-7 h-7 mx-auto mb-1.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-xs" style="color: var(--color-muted)">Sin documentos adjuntos</p>
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    {{-- Modal agregar parte --}}
    @if($showModalParte)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             style="background-color: rgba(0,0,0,0.4)">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md"
                 style="border: 1px solid var(--color-border)">

                {{-- Header modal --}}
                <div class="flex items-center justify-between px-5 py-4" style="border-bottom: 1px solid var(--color-border)">
                    <h3 class="text-sm font-semibold" style="color: var(--color-text)">Agregar parte procesal</h3>
                    <button wire:click="cerrarModalParte" type="button"
                            class="p-1 rounded-md hover:bg-slate-100 transition-colors"
                            style="color: var(--color-muted)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                {{-- Cuerpo modal --}}
                <div class="px-5 py-4 space-y-4">

                    {{-- Búsqueda persona --}}
                    <div class="space-y-1.5">
                        <label class="text-xs font-medium" style="color: var(--color-text)">Persona <span class="text-red-500">*</span></label>

                        @if($personaSeleccionadaId)
                            <div class="flex items-center justify-between px-3 py-2 rounded-md"
                                 style="background-color: var(--color-surface); border: 1px solid var(--color-border)">
                                <span class="text-xs font-medium" style="color: var(--color-text)">{{ $personaSeleccionadaNombre }}</span>
                                <button wire:click="$set('personaSeleccionadaId', '')" type="button"
                                        class="text-[11px] hover:underline" style="color: var(--color-muted)">Cambiar</button>
                            </div>
                        @else
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 pointer-events-none"
                                     style="color: var(--color-muted)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                <input wire:model.live.debounce.300ms="buscarPersonaModal"
                                       type="text"
                                       placeholder="Buscar por nombre o CI/NIT..."
                                       class="w-full h-8 pl-9 pr-3 text-xs rounded-md"
                                       style="border: 1px solid var(--color-border); outline: none;">
                            </div>

                            @if(count($resultadosModal) > 0)
                                <div class="rounded-md overflow-hidden" style="border: 1px solid var(--color-border)">
                                    @foreach($resultadosModal as $p)
                                        <button type="button"
                                                wire:click="seleccionarPersonaModal('{{ $p['id'] }}', '{{ addslashes($p['nombre_completo']) }}')"
                                                class="w-full text-left px-3 py-2 hover:bg-slate-50 transition-colors flex items-center justify-between gap-2 {{ !$loop->last ? 'border-b' : '' }}"
                                                style="{{ !$loop->last ? 'border-color: var(--color-border)' : '' }}">
                                            <span class="text-xs font-medium" style="color: var(--color-text)">{{ $p['nombre_completo'] }}</span>
                                            <span class="text-[11px] font-mono" style="color: var(--color-muted)">{{ $p['ci_nit'] }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            @elseif(strlen($buscarPersonaModal) >= 2)
                                <p class="text-xs" style="color: var(--color-muted)">
                                    Sin resultados —
                                    <a href="{{ route('personas.create') }}" wire:navigate
                                       class="font-medium hover:underline" style="color: var(--color-primary)">
                                        Registrar nueva persona
                                    </a>
                                </p>
                            @endif
                        @endif
                        @error('personaSeleccionadaId') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Rol procesal --}}
                    <div class="space-y-1.5">
                        <label class="text-xs font-medium" style="color: var(--color-text)">Rol procesal <span class="text-red-500">*</span></label>
                        <select wire:model="rolProcesal"
                                class="w-full h-8 px-3 text-xs rounded-md bg-white"
                                style="border: 1px solid var(--color-border); outline: none;">
                            <option value="demandante">Demandante</option>
                            <option value="demandado">Demandado</option>
                        </select>
                    </div>

                    {{-- Es cliente --}}
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="esCliente" class="w-3.5 h-3.5 rounded accent-emerald-600">
                        <span class="text-xs" style="color: var(--color-text)">Es cliente del estudio</span>
                    </label>
                </div>

                {{-- Footer modal --}}
                <div class="flex items-center justify-end gap-2 px-5 py-4" style="border-top: 1px solid var(--color-border)">
                    <button wire:click="cerrarModalParte" type="button"
                            class="h-8 px-4 text-xs rounded-md hover:bg-slate-50 transition-colors"
                            style="border: 1px solid var(--color-border); color: var(--color-text-secondary)">
                        Cancelar
                    </button>
                    <button wire:click="agregarParte" type="button"
                            wire:loading.attr="disabled"
                            class="h-8 px-4 text-xs font-medium text-white rounded-md hover:opacity-90 transition-opacity disabled:opacity-50"
                            style="background-color: var(--color-primary)">
                        <span wire:loading.remove wire:target="agregarParte">Agregar parte</span>
                        <span wire:loading wire:target="agregarParte">Guardando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
