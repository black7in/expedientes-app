<div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-lg font-semibold" style="color: var(--color-text)">Base de Conocimiento</h1>
            <p class="text-xs mt-0.5" style="color: var(--color-muted)">
                Alimenta el sistema RAG con leyes, jurisprudencia y documentos del estudio
            </p>
        </div>
        <button wire:click="cargarStats" class="text-xs px-3 py-1.5 rounded-md hover:opacity-80"
                style="border: 1px solid var(--color-border); color: var(--color-muted)">
            Actualizar stats
        </button>
    </div>

    {{-- Stats --}}
    @if(!empty($stats))
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg px-5 py-4" style="border: 1px solid var(--color-border)">
                <p class="text-[10px] font-semibold uppercase tracking-wide mb-1" style="color: var(--color-subtle)">Leyes indexadas</p>
                <p class="text-2xl font-bold" style="color: var(--color-text)">{{ $stats['leyes_count'] ?? 0 }}</p>
                <p class="text-[11px] mt-0.5" style="color: var(--color-muted)">{{ number_format($stats['leyes_chunks'] ?? 0) }} artículos</p>
            </div>
            <div class="bg-white rounded-lg px-5 py-4" style="border: 1px solid var(--color-border)">
                <p class="text-[10px] font-semibold uppercase tracking-wide mb-1" style="color: var(--color-subtle)">Autos Supremos</p>
                <p class="text-2xl font-bold" style="color: var(--color-text)">{{ number_format($tsjStats['resoluciones'] ?? 0) }}</p>
                <p class="text-[11px] mt-0.5" style="color: var(--color-muted)">{{ number_format($tsjStats['chunks'] ?? 0) }} fragmentos</p>
            </div>
            <div class="bg-white rounded-lg px-5 py-4" style="border: 1px solid var(--color-border)">
                <p class="text-[10px] font-semibold uppercase tracking-wide mb-1" style="color: var(--color-subtle)">Moldes de memoriales</p>
                <p class="text-2xl font-bold" style="color: var(--color-text)">{{ $stats['moldes_activos'] ?? 0 }}</p>
                <p class="text-[11px] mt-0.5" style="color: var(--color-muted)">{{ number_format($stats['moldes_total'] ?? 0) }} total</p>
            </div>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex gap-1 mb-4" style="border-bottom: 1px solid var(--color-border)">
        @foreach(['leyes' => 'Leyes bolivianas', 'jurisprudencia' => 'Jurisprudencia', 'plantillas' => 'Plantillas'] as $key => $label)
            <button wire:click="$set('tab', '{{ $key }}')"
                    class="px-4 py-2.5 text-xs font-medium transition-colors -mb-px"
                    style="{{ $tab === $key
                        ? 'color: var(--color-primary); border-bottom: 2px solid var(--color-primary)'
                        : 'color: var(--color-muted)' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ── TAB: LEYES ──────────────────────────────────────────────────────────── --}}
    @if($tab === 'leyes')
        <div class="flex gap-5 items-start">
            <div class="w-80 flex-shrink-0">
                <div class="bg-white rounded-lg p-4" style="border: 1px solid var(--color-border)">
                    <p class="text-[10px] font-semibold uppercase tracking-wide mb-3" style="color: var(--color-subtle)">Indexar nueva ley</p>

                    @if($mensajeLey)
                        <div class="flex items-start gap-2 px-3 py-2 rounded-md mb-3 text-xs"
                             style="{{ $exitoLey ? 'background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d' : 'background:#fef2f2;border:1px solid #fecaca;color:#b91c1c' }}">
                            {{ $mensajeLey }}
                        </div>
                    @endif

                    <div class="space-y-3">
                        <div>
                            <label class="block text-[11px] mb-1" style="color: var(--color-muted)">Nombre de la ley *</label>
                            <input type="text" wire:model="nombreLey" placeholder="Ej: Ley 439 — Código Procesal Civil"
                                   class="w-full text-xs rounded-md px-3 py-2 focus:outline-none"
                                   style="border: 1px solid var(--color-border); background: white;">
                            @error('nombreLey') <p class="text-[11px] mt-0.5 text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-[11px] mb-1" style="color: var(--color-muted)">Materia *</label>
                            <select wire:model="materiaLey" class="w-full text-xs rounded-md px-3 py-2 focus:outline-none"
                                    style="border: 1px solid var(--color-border); background: white;">
                                <option value="civil">Civil</option>
                                <option value="comercial">Comercial</option>
                                <option value="familiar">Familiar</option>
                                <option value="laboral">Laboral</option>
                                <option value="penal">Penal</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] mb-1" style="color: var(--color-muted)">Archivo PDF *</label>
                            <input type="file" wire:model="archivoPdf" accept=".pdf"
                                   class="w-full text-xs" style="color: var(--color-muted)">
                            @error('archivoPdf') <p class="text-[11px] mt-0.5 text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <button wire:click="indexarLey" wire:loading.attr="disabled" wire:target="indexarLey,archivoPdf"
                                class="w-full flex items-center justify-center gap-2 py-2 rounded-md text-xs font-medium disabled:opacity-50"
                                style="background-color: var(--color-primary); color: white;">
                            <svg wire:loading wire:target="indexarLey" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                            </svg>
                            <span wire:loading.remove wire:target="indexarLey">Indexar ley</span>
                            <span wire:loading wire:target="indexarLey">Indexando...</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex-1 min-w-0">
                <div class="bg-white rounded-lg" style="border: 1px solid var(--color-border)">
                    <div class="px-5 py-3.5" style="border-bottom: 1px solid var(--color-border)">
                        <p class="text-xs font-semibold uppercase tracking-wide" style="color: var(--color-muted)">
                            Leyes indexadas ({{ count($leyes) }})
                        </p>
                    </div>
                    @forelse($leyes as $ley)
                        <div class="flex items-center justify-between px-5 py-3"
                             @if(!$loop->first) style="border-top: 1px solid var(--color-border)" @endif>
                            <div>
                                <p class="text-xs font-medium" style="color: var(--color-text)">{{ $ley['ley'] }}</p>
                                <p class="text-[11px] capitalize mt-0.5" style="color: var(--color-muted)">{{ $ley['materia'] }}</p>
                            </div>
                            <span class="text-[11px]" style="color: var(--color-muted)">{{ number_format($ley['chunks']) }} artículos</span>
                        </div>
                    @empty
                        <div class="py-12 text-center">
                            <p class="text-xs" style="color: var(--color-muted)">No hay leyes indexadas aún</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    {{-- ── TAB: JURISPRUDENCIA ─────────────────────────────────────────────────── --}}
    @if($tab === 'jurisprudencia')

        @if(empty($tsjStats))
            <div class="flex items-start gap-2 px-4 py-3 rounded-lg text-xs mb-5"
                 style="background:#fef2f2;border:1px solid #fecaca;color:#b91c1c">
                <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
                Servicio de jurisprudencia TSJ no disponible. Verificá que el contenedor esté corriendo.
            </div>
        @endif

        {{-- Stats cards --}}
        <div class="grid grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg px-5 py-4" style="border: 1px solid var(--color-border)">
                <p class="text-[10px] font-semibold uppercase tracking-wide mb-1" style="color: var(--color-subtle)">Resoluciones indexadas</p>
                <p class="text-2xl font-bold" style="color: var(--color-text)">{{ number_format($tsjStats['resoluciones'] ?? 0) }}</p>
                <p class="text-[11px] mt-0.5" style="color: var(--color-muted)">Autos Supremos del TSJ Bolivia</p>
            </div>
            <div class="bg-white rounded-lg px-5 py-4" style="border: 1px solid var(--color-border)">
                <p class="text-[10px] font-semibold uppercase tracking-wide mb-1" style="color: var(--color-subtle)">Fragmentos vectorizados</p>
                <p class="text-2xl font-bold" style="color: var(--color-text)">{{ number_format($tsjStats['chunks'] ?? 0) }}</p>
                <p class="text-[11px] mt-0.5" style="color: var(--color-muted)">Índice semántico (pgvector)</p>
            </div>
            <div class="bg-white rounded-lg px-5 py-4" style="border: 1px solid var(--color-border)">
                <p class="text-[10px] font-semibold uppercase tracking-wide mb-1" style="color: var(--color-subtle)">Fuente</p>
                <p class="text-sm font-semibold mt-1" style="color: var(--color-text)">GÉNESIS TSJ</p>
                <p class="text-[11px] mt-0.5" style="color: var(--color-muted)">Indexación automática al consultar</p>
            </div>
        </div>

        {{-- Breakdown por materia --}}
        <div class="bg-white rounded-lg" style="border: 1px solid var(--color-border)">
            <div class="px-5 py-3.5 flex items-center justify-between" style="border-bottom: 1px solid var(--color-border)">
                <p class="text-xs font-semibold uppercase tracking-wide" style="color: var(--color-muted)">Resoluciones por materia</p>
                <a href="{{ config('services.tsj.public_url') }}" target="_blank"
                   class="text-[11px] flex items-center gap-1 hover:opacity-70 transition-opacity"
                   style="color: var(--color-primary)">
                    Ir al buscador
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                </a>
            </div>
            @forelse($tsjStats['por_materia'] ?? [] as $fila)
                @php
                    $total = $tsjStats['resoluciones'] ?? 1;
                    $pct   = $total > 0 ? round($fila['count'] / $total * 100) : 0;
                @endphp
                <div class="flex items-center gap-4 px-5 py-3"
                     @if(!$loop->first) style="border-top: 1px solid var(--color-border)" @endif>
                    <div class="w-28 flex-shrink-0">
                        <p class="text-xs font-medium" style="color: var(--color-text)">{{ $fila['materia'] }}</p>
                    </div>
                    <div class="flex-1 h-1.5 rounded-full" style="background: var(--color-border)">
                        <div class="h-1.5 rounded-full" style="width: {{ $pct }}%; background-color: var(--color-primary)"></div>
                    </div>
                    <span class="w-16 text-right text-[11px]" style="color: var(--color-muted)">
                        {{ number_format($fila['count']) }}
                    </span>
                </div>
            @empty
                <div class="py-10 text-center">
                    <p class="text-xs" style="color: var(--color-muted)">Sin datos disponibles</p>
                </div>
            @endforelse
        </div>

    @endif

    {{-- ── TAB: PLANTILLAS ─────────────────────────────────────────────────────── --}}
    @if($tab === 'plantillas')

        {{-- Flash message --}}
        @if($mensajePlantilla)
            <div class="mb-4 flex items-center gap-2 px-4 py-2.5 rounded-md text-xs font-medium"
                 style="{{ $exitoPlantilla
                    ? 'background-color: var(--color-primary-light); color: var(--color-primary-text)'
                    : 'background:#fef2f2;border:1px solid #fecaca;color:#b91c1c' }}">
                {{ $mensajePlantilla }}
            </div>
        @endif

        {{-- Vista: lista --}}
        @if($plantillaVista === 'lista')

            {{-- Toolbar --}}
            <div class="flex items-center justify-between mb-4 gap-4">
                <div class="flex items-center gap-2 flex-1">
                    <div class="relative flex-1 max-w-xs">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input wire:model.live.debounce.300ms="plantillaBusqueda" type="search"
                               placeholder="Buscar por nombre o subtipo..."
                               class="w-full h-8 pl-9 pr-3 text-xs border border-slate-200 rounded-md bg-white
                                      focus:outline-none focus:ring-2 focus:ring-slate-900/10 placeholder:text-slate-400">
                    </div>
                    <select wire:model.live="plantillaTipoFiltro"
                            class="h-8 px-2.5 text-xs border border-slate-200 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10">
                        <option value="">Todos los tipos</option>
                        <option value="demanda">Demanda</option>
                        <option value="memorial">Memorial</option>
                        <option value="contestacion">Contestación</option>
                        <option value="apelacion">Apelación</option>
                        <option value="contrato">Contrato</option>
                        <option value="nulidad">Nulidad</option>
                    </select>
                </div>
                <button wire:click="nuevaPlantilla"
                        class="inline-flex items-center gap-1.5 h-8 px-3 text-white text-xs font-medium rounded-md flex-shrink-0"
                        style="background-color: var(--color-primary)"
                        onmouseover="this.style.backgroundColor='var(--color-primary-hover)'"
                        onmouseout="this.style.backgroundColor='var(--color-primary)'">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva plantilla
                </button>
            </div>

            {{-- Tabla --}}
            <div class="bg-white rounded-lg overflow-hidden" style="border: 1px solid var(--color-border)">
                <table class="w-full text-xs">
                    <thead>
                        <tr style="border-bottom: 1px solid var(--color-border)">
                            <th class="text-left px-4 py-2.5 font-semibold text-slate-600 uppercase tracking-wide text-[10px]">Nombre</th>
                            <th class="text-left px-4 py-2.5 font-semibold text-slate-600 uppercase tracking-wide text-[10px]">Tipo / Subtipo</th>
                            <th class="text-left px-4 py-2.5 font-semibold text-slate-600 uppercase tracking-wide text-[10px]">Secciones</th>
                            <th class="text-left px-4 py-2.5 font-semibold text-slate-600 uppercase tracking-wide text-[10px]">Versión</th>
                            <th class="text-left px-4 py-2.5 font-semibold text-slate-600 uppercase tracking-wide text-[10px]">Estado</th>
                            <th class="px-4 py-2.5"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($plantillas as $plantilla)
                            <tr class="hover:bg-slate-50 transition-colors group"
                                @if(!$loop->first) style="border-top: 1px solid var(--color-border)" @endif>
                                <td class="px-4 py-3 font-medium text-slate-900">
                                    {{ $plantilla->nombre }}
                                    @if($plantilla->descripcion)
                                        <p class="font-normal text-[11px] mt-0.5 max-w-xs truncate" style="color: var(--color-muted)">
                                            {{ $plantilla->descripcion }}
                                        </p>
                                    @endif
                                </td>
                                <td class="px-4 py-3" style="color: var(--color-text-secondary)">
                                    <span class="capitalize">{{ $plantilla->tipo_documento }}</span>
                                    <span style="color: var(--color-subtle)"> / </span>
                                    <span class="capitalize">{{ $plantilla->subtipo }}</span>
                                </td>
                                <td class="px-4 py-3 tabular-nums" style="color: var(--color-muted)">
                                    {{ count($plantilla->secciones ?? []) }}
                                </td>
                                <td class="px-4 py-3 font-mono text-[11px]" style="color: var(--color-muted)">
                                    {{ $plantilla->version }}
                                </td>
                                <td class="px-4 py-3">
                                    <button wire:click="togglePlantillaActiva('{{ $plantilla->id }}')"
                                            type="button"
                                            class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200"
                                            style="{{ $plantilla->activa ? 'background-color: var(--color-primary)' : 'background-color: #d1d5db;' }}">
                                        <span class="pointer-events-none inline-block h-4 w-4 rounded-full bg-white shadow transform transition duration-200 {{ $plantilla->activa ? 'translate-x-4' : 'translate-x-0' }}"></span>
                                    </button>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($confirmarEliminarId === $plantilla->id)
                                        <div class="inline-flex items-center gap-2">
                                            <span class="text-[11px]" style="color: var(--color-muted)">¿Eliminar?</span>
                                            <button wire:click="eliminarPlantilla('{{ $plantilla->id }}')"
                                                    class="text-[11px] font-medium text-red-600 hover:text-red-800">Sí</button>
                                            <button wire:click="cancelarEliminar"
                                                    class="text-[11px]" style="color: var(--color-muted)">No</button>
                                        </div>
                                    @else
                                        <div class="opacity-0 group-hover:opacity-100 inline-flex items-center gap-3 transition-all">
                                            <button wire:click="editarPlantilla('{{ $plantilla->id }}')"
                                                    class="text-slate-500 hover:text-slate-900 transition-colors text-xs flex items-center gap-1">
                                                Editar
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </button>
                                            <button wire:click="confirmarEliminar('{{ $plantilla->id }}')"
                                                    class="text-[11px] text-red-500 hover:text-red-700">
                                                Eliminar
                                            </button>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p class="text-xs" style="color: var(--color-subtle)">No se encontraron plantillas</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        {{-- Vista: formulario --}}
        @else

            {{-- Breadcrumb interno --}}
            <div class="flex items-center gap-1.5 text-xs mb-5" style="color: var(--color-subtle)">
                <button wire:click="cancelarFormularioPlantilla" class="hover:text-slate-700 transition-colors">
                    Plantillas
                </button>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
                <span style="color: var(--color-text-secondary)">
                    {{ $editandoPlantillaId ? $plantillaNombre : 'Nueva plantilla' }}
                </span>
            </div>

            <div class="max-w-3xl space-y-5">

                {{-- Datos básicos --}}
                <div class="bg-white rounded-lg p-5 space-y-4" style="border: 1px solid var(--color-border)">
                    <h2 class="text-xs font-semibold uppercase tracking-wide" style="color: var(--color-muted)">Datos de la plantilla</h2>

                    <div class="space-y-1.5">
                        <label class="text-xs font-medium text-slate-700">Nombre <span class="text-red-500">*</span></label>
                        <input wire:model="plantillaNombre" type="text" placeholder="Ej: Demanda Ejecutiva"
                               class="w-full h-8 px-3 text-xs border rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900/10 @error('plantillaNombre') border-red-400 @else border-slate-200 @enderror">
                        @error('plantillaNombre') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-slate-700">Tipo de documento <span class="text-red-500">*</span></label>
                            <select wire:model="plantillaTipoDocumento"
                                    class="w-full h-8 px-3 text-xs border rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10 @error('plantillaTipoDocumento') border-red-400 @else border-slate-200 @enderror">
                                <option value="demanda">Demanda</option>
                                <option value="memorial">Memorial</option>
                                <option value="contestacion">Contestación</option>
                                <option value="apelacion">Apelación</option>
                                <option value="contrato">Contrato</option>
                                <option value="nulidad">Nulidad</option>
                            </select>
                            @error('plantillaTipoDocumento') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-slate-700">Subtipo <span class="text-red-500">*</span></label>
                            <input wire:model="plantillaSubtipo" type="text" placeholder="Ej: ejecutiva"
                                   class="w-full h-8 px-3 text-xs border rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900/10 @error('plantillaSubtipo') border-red-400 @else border-slate-200 @enderror">
                            @error('plantillaSubtipo') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-medium text-slate-700">Descripción</label>
                        <textarea wire:model="plantillaDescripcion" rows="2"
                                  placeholder="Descripción breve del caso de uso..."
                                  class="w-full px-3 py-2 text-xs border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900/10 resize-none"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-slate-700">Versión <span class="text-red-500">*</span></label>
                            <input wire:model="plantillaVersion" type="text" placeholder="v1"
                                   class="w-full h-8 px-3 text-xs border rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900/10 @error('plantillaVersion') border-red-400 @else border-slate-200 @enderror">
                            @error('plantillaVersion') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-medium text-slate-700">Estado</label>
                            <select wire:model="plantillaActiva"
                                    class="w-full h-8 px-3 text-xs border border-slate-200 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10">
                                <option value="1">Activa</option>
                                <option value="0">Inactiva</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Secciones JSON --}}
                <div class="bg-white rounded-lg p-5 space-y-3" style="border: 1px solid var(--color-border)">
                    <div>
                        <h2 class="text-xs font-semibold uppercase tracking-wide" style="color: var(--color-muted)">Secciones</h2>
                        <p class="text-[11px] mt-0.5" style="color: var(--color-subtle)">
                            Array JSON. Cada sección debe tener: <span class="font-mono">id, nombre, orden, obligatoria, instrucciones, retrieval, salida</span>
                        </p>
                    </div>
                    <textarea wire:model="plantillaSecciones" rows="20" spellcheck="false"
                              class="w-full px-3 py-2.5 text-xs font-mono border rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900/10 @error('plantillaSecciones') border-red-400 @else border-slate-200 @enderror"
                              style="line-height: 1.6"></textarea>
                    @error('plantillaSecciones') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Acciones --}}
                <div class="flex items-center gap-2 justify-end">
                    <button wire:click="cancelarFormularioPlantilla"
                            class="h-8 px-4 text-xs border border-slate-200 rounded-md hover:bg-slate-50 transition-colors inline-flex items-center"
                            style="color: var(--color-muted)">
                        Cancelar
                    </button>
                    <button wire:click="guardarPlantilla"
                            wire:loading.attr="disabled"
                            wire:target="guardarPlantilla"
                            class="h-8 px-4 text-white text-xs font-medium rounded-md transition-colors disabled:opacity-50 inline-flex items-center gap-1.5"
                            style="background-color: var(--color-primary)"
                            onmouseover="this.style.backgroundColor='var(--color-primary-hover)'"
                            onmouseout="this.style.backgroundColor='var(--color-primary)'">
                        <span wire:loading.remove wire:target="guardarPlantilla">
                            {{ $editandoPlantillaId ? 'Guardar cambios' : 'Crear plantilla' }}
                        </span>
                        <span wire:loading wire:target="guardarPlantilla" class="flex items-center gap-1.5">
                            <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Guardando...
                        </span>
                    </button>
                </div>
            </div>
        @endif

    @endif

    {{-- Loading overlay --}}
    <div wire:loading.delay class="fixed inset-0 bg-white/30 backdrop-blur-[1px] z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg px-4 py-2.5 flex items-center gap-2 shadow-sm text-xs"
             style="border: 1px solid var(--color-border); color: var(--color-muted)">
            <svg class="animate-spin w-3.5 h-3.5" style="color: var(--color-subtle)" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            Cargando...
        </div>
    </div>

</div>
