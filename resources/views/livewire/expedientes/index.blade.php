<div>
    {{-- Toolbar --}}
    <div class="flex items-center justify-between mb-5 gap-4">
        <div class="flex items-center gap-2 flex-1">
            {{-- Búsqueda --}}
            <div class="relative flex-1 max-w-xs">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    wire:model.live.debounce.300ms="busqueda"
                    type="search"
                    placeholder="Buscar por nro., parte, juzgado..."
                    class="w-full h-8 pl-9 pr-3 text-xs border border-slate-200 rounded-md bg-white
                           focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400
                           placeholder:text-slate-400"
                >
            </div>

            {{-- Filtro estado --}}
            <select wire:model.live="estado"
                    class="h-8 px-2.5 text-xs border border-slate-200 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10">
                <option value="">Todos los estados</option>
                <option value="activo">Activo</option>
                <option value="suspendido">Suspendido</option>
                <option value="en_apelacion">En apelación</option>
                <option value="archivado">Archivado</option>
                <option value="concluido">Concluido</option>
            </select>

            {{-- Filtro tipo proceso --}}
            <select wire:model.live="tipoProceso"
                    class="h-8 px-2.5 text-xs border border-slate-200 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10">
                <option value="">Tipo de proceso</option>
                @foreach($tiposProceso as $tipo)
                    <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                @endforeach
            </select>

            @if($hayFiltros)
                <button wire:click="limpiarFiltros"
                        class="h-8 px-2.5 text-xs text-slate-500 hover:text-slate-900 border border-slate-200 rounded-md hover:bg-slate-50 transition-colors flex items-center gap-1.5">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Limpiar
                </button>
            @endif
        </div>

        <a href="{{ route('expedientes.create') }}" wire:navigate
           class="inline-flex items-center gap-1.5 h-8 px-3 text-white text-xs font-medium rounded-md hover:opacity-90 transition-opacity flex-shrink-0"
           style="background-color: var(--color-primary)">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo expediente
        </a>
    </div>

    {{-- Tabla --}}
    <div class="bg-white border border-slate-200 rounded-lg overflow-hidden">
        <table class="w-full text-xs">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50">
                    <th class="text-left px-4 py-2.5 font-semibold text-slate-600 uppercase tracking-wide text-[10px]">Nro. Expediente</th>
                    <th class="text-left px-4 py-2.5 font-semibold text-slate-600 uppercase tracking-wide text-[10px]">Partes</th>
                    <th class="text-left px-4 py-2.5 font-semibold text-slate-600 uppercase tracking-wide text-[10px]">Tipo proceso</th>
                    <th class="text-left px-4 py-2.5 font-semibold text-slate-600 uppercase tracking-wide text-[10px]">Juzgado</th>
                    <th class="text-left px-4 py-2.5 font-semibold text-slate-600 uppercase tracking-wide text-[10px]">Estado</th>
                    <th class="text-left px-4 py-2.5 font-semibold text-slate-600 uppercase tracking-wide text-[10px]">Inicio</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($expedientes as $expediente)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-4 py-3 font-mono font-medium text-slate-900">
                            {{ $expediente->numero_expediente ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            @foreach($expediente->partes->take(2) as $parte)
                                <div class="text-slate-700">
                                    {{ $parte->persona->nombre_completo }}
                                    <span class="text-slate-400">({{ $parte->rol_procesal }})</span>
                                </div>
                            @endforeach
                            @if($expediente->partes->count() > 2)
                                <span class="text-slate-400">+{{ $expediente->partes->count() - 2 }} más</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $expediente->tipoProceso->nombre }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $expediente->juzgado->nombre }}</td>
                        <td class="px-4 py-3">
                            <x-estado-badge :estado="$expediente->estado" />
                        </td>
                        <td class="px-4 py-3 text-slate-500 tabular-nums">
                            {{ $expediente->fecha_inicio->format('d/m/Y') }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('expedientes.show', $expediente) }}" wire:navigate
                               class="opacity-0 group-hover:opacity-100 inline-flex items-center gap-1 text-slate-500 hover:text-slate-900 transition-all">
                                Ver
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                            <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            No se encontraron expedientes
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($expedientes->hasPages())
            <div class="border-t border-slate-200 px-4 py-3">
                {{ $expedientes->links() }}
            </div>
        @endif
    </div>

    {{-- Loading overlay reactivo --}}
    <div wire:loading.delay class="fixed inset-0 bg-white/30 backdrop-blur-[1px] z-50 flex items-center justify-center">
        <div class="bg-white border border-slate-200 rounded-lg px-4 py-2.5 flex items-center gap-2 shadow-sm text-xs text-slate-600">
            <svg class="animate-spin w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            Buscando...
        </div>
    </div>
</div>
