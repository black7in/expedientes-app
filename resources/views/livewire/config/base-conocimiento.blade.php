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
                <p class="text-2xl font-bold" style="color: var(--color-text)">{{ $stats['autos_count'] ?? 0 }}</p>
                <p class="text-[11px] mt-0.5" style="color: var(--color-muted)">{{ number_format($stats['juris_chunks'] ?? 0) }} fragmentos</p>
            </div>
            <div class="bg-white rounded-lg px-5 py-4" style="border: 1px solid var(--color-border)">
                <p class="text-[10px] font-semibold uppercase tracking-wide mb-1" style="color: var(--color-subtle)">Documentos del estudio</p>
                <p class="text-2xl font-bold" style="color: var(--color-text)">{{ $stats['docs_count'] ?? 0 }}</p>
                <p class="text-[11px] mt-0.5" style="color: var(--color-muted)">{{ number_format($stats['doc_chunks'] ?? 0) }} fragmentos</p>
            </div>
        </div>
    @endif

    {{-- Tabs --}}
    <div class="flex gap-1 mb-4" style="border-bottom: 1px solid var(--color-border)">
        @foreach(['leyes' => 'Leyes bolivianas', 'jurisprudencia' => 'Jurisprudencia', 'documentos' => 'Documentos del estudio', 'plantillas' => 'Plantillas de prompts'] as $key => $label)
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

            {{-- Formulario --}}
            <div class="w-80 flex-shrink-0 space-y-4">
                <div class="bg-white rounded-lg p-4" style="border: 1px solid var(--color-border)">
                    <p class="text-[10px] font-semibold uppercase tracking-wide mb-3" style="color: var(--color-subtle)">
                        Indexar nueva ley
                    </p>

                    @if($mensajeLey)
                        <div class="flex items-start gap-2 px-3 py-2 rounded-md mb-3 text-xs"
                             style="{{ $exitoLey ? 'background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d' : 'background:#fef2f2;border:1px solid #fecaca;color:#b91c1c' }}">
                            {{ $mensajeLey }}
                        </div>
                    @endif

                    <div class="space-y-3">
                        <div>
                            <label class="block text-[11px] mb-1" style="color: var(--color-muted)">Nombre de la ley *</label>
                            <input type="text" wire:model="nombreLey"
                                   placeholder="Ej: Ley 439 — Código Procesal Civil"
                                   class="w-full text-xs rounded-md px-3 py-2 focus:outline-none"
                                   style="border: 1px solid var(--color-border); color: var(--color-text); background: white;">
                            @error('nombreLey') <p class="text-[11px] mt-0.5 text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-[11px] mb-1" style="color: var(--color-muted)">Materia *</label>
                            <select wire:model="materiaLey"
                                    class="w-full text-xs rounded-md px-3 py-2 focus:outline-none"
                                    style="border: 1px solid var(--color-border); color: var(--color-text); background: white;">
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

                        <button wire:click="indexarLey"
                                wire:loading.attr="disabled"
                                wire:target="indexarLey,archivoPdf"
                                class="w-full flex items-center justify-center gap-2 py-2 rounded-md text-xs font-medium disabled:opacity-50"
                                style="background-color: var(--color-primary); color: white;">
                            <svg wire:loading wire:target="indexarLey"
                                 class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                            </svg>
                            <span wire:loading.remove wire:target="indexarLey">Indexar ley</span>
                            <span wire:loading wire:target="indexarLey">Indexando...</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Lista de leyes indexadas --}}
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
                            <span class="text-[11px]" style="color: var(--color-muted)">
                                {{ number_format($ley['chunks']) }} artículos
                            </span>
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
        <div class="flex gap-5 items-start">

            {{-- Formulario --}}
            <div class="w-80 flex-shrink-0 space-y-4">
                <div class="bg-white rounded-lg p-4" style="border: 1px solid var(--color-border)">
                    <p class="text-[10px] font-semibold uppercase tracking-wide mb-3" style="color: var(--color-subtle)">
                        Agregar Auto Supremo
                    </p>

                    @if($mensajeAuto)
                        <div class="flex items-start gap-2 px-3 py-2 rounded-md mb-3 text-xs"
                             style="{{ $exitoAuto ? 'background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d' : 'background:#fef2f2;border:1px solid #fecaca;color:#b91c1c' }}">
                            {{ $mensajeAuto }}
                        </div>
                    @endif

                    <div class="space-y-3">
                        <div>
                            <label class="block text-[11px] mb-1" style="color: var(--color-muted)">Número de Auto Supremo *</label>
                            <input type="text" wire:model="numeroAuto"
                                   placeholder="Ej: AS 123/2023"
                                   class="w-full text-xs rounded-md px-3 py-2 focus:outline-none"
                                   style="border: 1px solid var(--color-border); color: var(--color-text); background: white;">
                            @error('numeroAuto') <p class="text-[11px] mt-0.5 text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] mb-1" style="color: var(--color-muted)">Materia *</label>
                                <select wire:model="materiaAuto"
                                        class="w-full text-xs rounded-md px-3 py-2 focus:outline-none"
                                        style="border: 1px solid var(--color-border); color: var(--color-text); background: white;">
                                    <option value="civil">Civil</option>
                                    <option value="comercial">Comercial</option>
                                    <option value="familiar">Familiar</option>
                                    <option value="laboral">Laboral</option>
                                    <option value="penal">Penal</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[11px] mb-1" style="color: var(--color-muted)">Fecha</label>
                                <input type="date" wire:model="fechaAuto"
                                       class="w-full text-xs rounded-md px-3 py-2 focus:outline-none"
                                       style="border: 1px solid var(--color-border); color: var(--color-text); background: white;">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] mb-1" style="color: var(--color-muted)">Sala</label>
                            <input type="text" wire:model="salaAuto"
                                   placeholder="Ej: Sala Civil Primera"
                                   class="w-full text-xs rounded-md px-3 py-2 focus:outline-none"
                                   style="border: 1px solid var(--color-border); color: var(--color-text); background: white;">
                        </div>

                        <div>
                            <label class="block text-[11px] mb-1" style="color: var(--color-muted)">Texto del Auto Supremo *</label>
                            <textarea wire:model="textoAuto" rows="8"
                                      placeholder="Pega aquí el texto completo del Auto Supremo..."
                                      class="w-full text-xs rounded-md px-3 py-2 focus:outline-none resize-none"
                                      style="border: 1px solid var(--color-border); color: var(--color-text); background: white;"></textarea>
                            @error('textoAuto') <p class="text-[11px] mt-0.5 text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <button wire:click="indexarJurisprudencia"
                                wire:loading.attr="disabled"
                                wire:target="indexarJurisprudencia"
                                class="w-full flex items-center justify-center gap-2 py-2 rounded-md text-xs font-medium disabled:opacity-50"
                                style="background-color: var(--color-primary); color: white;">
                            <svg wire:loading wire:target="indexarJurisprudencia"
                                 class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                            </svg>
                            <span wire:loading.remove wire:target="indexarJurisprudencia">Indexar Auto Supremo</span>
                            <span wire:loading wire:target="indexarJurisprudencia">Indexando...</span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Lista de autos indexados --}}
            <div class="flex-1 min-w-0">
                <div class="bg-white rounded-lg" style="border: 1px solid var(--color-border)">
                    <div class="px-5 py-3.5" style="border-bottom: 1px solid var(--color-border)">
                        <p class="text-xs font-semibold uppercase tracking-wide" style="color: var(--color-muted)">
                            Autos Supremos indexados ({{ count($autos) }})
                        </p>
                    </div>
                    @forelse($autos as $auto)
                        <div class="flex items-center justify-between px-5 py-3"
                             @if(!$loop->first) style="border-top: 1px solid var(--color-border)" @endif>
                            <div>
                                <p class="text-xs font-medium font-mono" style="color: var(--color-text)">{{ $auto['numero_auto'] }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-[11px] capitalize" style="color: var(--color-muted)">{{ $auto['materia'] }}</span>
                                    @if($auto['fecha'])
                                        <span style="color: var(--color-border-strong)">·</span>
                                        <span class="text-[11px]" style="color: var(--color-muted)">{{ $auto['fecha'] }}</span>
                                    @endif
                                </div>
                            </div>
                            <span class="text-[11px]" style="color: var(--color-muted)">
                                {{ $auto['chunks'] }} fragmentos
                            </span>
                        </div>
                    @empty
                        <div class="py-12 text-center">
                            <p class="text-xs" style="color: var(--color-muted)">No hay jurisprudencia indexada aún</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    {{-- ── TAB: DOCUMENTOS ─────────────────────────────────────────────────────── --}}
    @if($tab === 'documentos')
        <div class="bg-white rounded-lg" style="border: 1px solid var(--color-border)">
            <div class="px-5 py-3.5" style="border-bottom: 1px solid var(--color-border)">
                <p class="text-xs font-semibold uppercase tracking-wide" style="color: var(--color-muted)">
                    Documentos del estudio indexados ({{ count($documentosIndexados) }})
                </p>
            </div>
            @forelse($documentosIndexados as $doc)
                <div class="flex items-center justify-between px-5 py-3"
                     @if(!$loop->first) style="border-top: 1px solid var(--color-border)" @endif>
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-6 h-6 rounded flex items-center justify-center flex-shrink-0 text-[10px] font-bold"
                             style="{{ $doc->formato === 'pdf' ? 'background:#fee2e2;color:#b91c1c' : 'background:#dbeafe;color:#1d4ed8' }}">
                            {{ strtoupper($doc->formato) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-mono truncate" style="color: var(--color-text)">{{ $doc->nombre_archivo }}</p>
                            <div class="flex items-center gap-2 mt-0.5">
                                <span class="text-[11px] capitalize" style="color: var(--color-muted)">{{ $doc->tipo_documento }}</span>
                                @if($doc->expediente)
                                    <span style="color: var(--color-border-strong)">·</span>
                                    <span class="text-[11px] font-mono" style="color: var(--color-primary)">
                                        {{ $doc->expediente->numero_expediente ?? 'Sin número' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('documentos.show', $doc) }}" wire:navigate
                       class="text-[11px] hover:underline flex-shrink-0" style="color: var(--color-muted)">
                        Ver →
                    </a>
                </div>
            @empty
                <div class="py-12 text-center">
                    <p class="text-xs mb-1" style="color: var(--color-muted)">No hay documentos indexados aún</p>
                    <p class="text-[11px]" style="color: var(--color-subtle)">
                        Sube un documento y usa el botón "Indexar para RAG" en la página del documento
                    </p>
                </div>
            @endforelse
        </div>
    @endif

    {{-- ── TAB: PLANTILLAS ──────────────────────────────────────────────────────── --}}
    @if($tab === 'plantillas')
        <div class="flex gap-5 items-start">

            {{-- Formulario crear/editar --}}
            <div class="w-80 flex-shrink-0">
                <div class="bg-white rounded-lg p-4" style="border: 1px solid var(--color-border)">
                    <p class="text-[10px] font-semibold uppercase tracking-wide mb-3" style="color: var(--color-subtle)">
                        {{ $editandoId ? 'Editar plantilla' : 'Nueva plantilla' }}
                    </p>

                    @if($mensajePlantilla)
                        <div class="flex items-start gap-2 px-3 py-2 rounded-md mb-3 text-xs"
                             style="{{ $exitoPlantilla ? 'background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d' : 'background:#fef2f2;border:1px solid #fecaca;color:#b91c1c' }}">
                            {{ $mensajePlantilla }}
                        </div>
                    @endif

                    <div class="space-y-3">

                        <div>
                            <label class="block text-[11px] mb-1" style="color: var(--color-muted)">Nombre *</label>
                            <input type="text" wire:model="plantillaNombre"
                                   placeholder="Ej: Demanda Civil Ordinaria"
                                   class="w-full text-xs rounded-md px-3 py-2 focus:outline-none"
                                   style="border: 1px solid var(--color-border); color: var(--color-text); background: white;">
                            @error('plantillaNombre') <p class="text-[11px] mt-0.5 text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-[11px] mb-1" style="color: var(--color-muted)">Tipo *</label>
                                <select wire:model="plantillaTipo"
                                        class="w-full text-xs rounded-md px-3 py-2 focus:outline-none"
                                        style="border: 1px solid var(--color-border); color: var(--color-text); background: white;">
                                    <option value="demanda">Demanda</option>
                                    <option value="memorial">Memorial</option>
                                    <option value="contestacion">Contestación</option>
                                    <option value="apelacion">Apelación</option>
                                    <option value="nulidad">Nulidad</option>
                                    <option value="contrato">Contrato</option>
                                </select>
                                @error('plantillaTipo') <p class="text-[11px] mt-0.5 text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[11px] mb-1" style="color: var(--color-muted)">Materia *</label>
                                <select wire:model="plantillaMateria"
                                        class="w-full text-xs rounded-md px-3 py-2 focus:outline-none"
                                        style="border: 1px solid var(--color-border); color: var(--color-text); background: white;">
                                    <option value="civil">Civil</option>
                                    <option value="comercial">Comercial</option>
                                    <option value="familiar">Familiar</option>
                                    <option value="laboral">Laboral</option>
                                    <option value="penal">Penal</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[11px] mb-1" style="color: var(--color-muted)">Prompt del sistema *</label>
                            <textarea wire:model="plantillaContenido" rows="12"
                                      placeholder="Escribe las instrucciones que Claude recibirá como system prompt para este tipo de documento..."
                                      class="w-full text-xs rounded-md px-3 py-2 focus:outline-none resize-y font-mono"
                                      style="border: 1px solid var(--color-border); color: var(--color-text); background: white;"></textarea>
                            @error('plantillaContenido') <p class="text-[11px] mt-0.5 text-red-600">{{ $message }}</p> @enderror
                        </div>

                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" wire:model="plantillaEsDefault"
                                   class="rounded" style="accent-color: var(--color-primary)">
                            <span class="text-[11px]" style="color: var(--color-muted)">
                                Usar como prompt por defecto para este tipo/materia
                            </span>
                        </label>

                        <div class="flex gap-2">
                            <button wire:click="guardarPlantilla"
                                    wire:loading.attr="disabled"
                                    wire:target="guardarPlantilla"
                                    class="flex-1 flex items-center justify-center gap-1.5 py-2 rounded-md text-xs font-medium disabled:opacity-50"
                                    style="background-color: var(--color-primary); color: white;">
                                <svg wire:loading wire:target="guardarPlantilla"
                                     class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                </svg>
                                <span wire:loading.remove wire:target="guardarPlantilla">
                                    {{ $editandoId ? 'Actualizar' : 'Crear plantilla' }}
                                </span>
                                <span wire:loading wire:target="guardarPlantilla">Guardando...</span>
                            </button>

                            @if($editandoId)
                                <button wire:click="nuevaPlantilla"
                                        class="px-3 py-2 rounded-md text-xs"
                                        style="border: 1px solid var(--color-border); color: var(--color-muted)">
                                    Cancelar
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Lista de plantillas agrupadas por tipo --}}
            <div class="flex-1 min-w-0 space-y-4">

                @php
                    $tiposLabel = [
                        'demanda'      => 'Demanda',
                        'memorial'     => 'Memorial de trámite',
                        'contestacion' => 'Contestación de demanda',
                        'apelacion'    => 'Recurso de apelación',
                        'nulidad'      => 'Recurso de nulidad',
                        'contrato'     => 'Contrato civil',
                    ];
                @endphp

                @forelse($plantillasPorTipo as $tipo => $grupo)
                    <div class="bg-white rounded-lg" style="border: 1px solid var(--color-border)">
                        <div class="flex items-center justify-between px-5 py-3"
                             style="border-bottom: 1px solid var(--color-border)">
                            <p class="text-xs font-semibold" style="color: var(--color-text)">
                                {{ $tiposLabel[$tipo] ?? $tipo }}
                            </p>
                            <span class="text-[11px]" style="color: var(--color-muted)">
                                {{ count($grupo) }} plantilla(s)
                            </span>
                        </div>

                        @foreach($grupo as $plantilla)
                            <div class="flex items-center justify-between px-5 py-3"
                                 @if(!$loop->first) style="border-top: 1px solid var(--color-border)" @endif>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="text-xs font-medium truncate" style="color: var(--color-text)">
                                            {{ $plantilla['nombre'] }}
                                        </p>
                                        @if($plantilla['es_default'])
                                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold"
                                                  style="background-color: #dbeafe; color: #1d4ed8">
                                                DEFAULT
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] capitalize mt-0.5" style="color: var(--color-muted)">
                                        {{ $plantilla['materia'] }} · {{ number_format($plantilla['contenido_len']) }} caracteres
                                    </p>
                                </div>

                                <div class="flex items-center gap-1 flex-shrink-0 ml-3">
                                    @if(!$plantilla['es_default'])
                                        <button wire:click="setDefault('{{ $plantilla['id'] }}')"
                                                class="text-[11px] px-2 py-1 rounded hover:opacity-80"
                                                style="border: 1px solid var(--color-border); color: var(--color-muted)"
                                                title="Marcar como default">
                                            ★
                                        </button>
                                    @endif
                                    <button wire:click="editarPlantilla('{{ $plantilla['id'] }}')"
                                            class="text-[11px] px-2 py-1 rounded hover:opacity-80"
                                            style="border: 1px solid var(--color-border); color: var(--color-muted)">
                                        Editar
                                    </button>
                                    <button wire:click="eliminarPlantilla('{{ $plantilla['id'] }}')"
                                            wire:confirm="¿Eliminar esta plantilla?"
                                            class="text-[11px] px-2 py-1 rounded hover:opacity-80"
                                            style="border: 1px solid #fecaca; color: #b91c1c">
                                        ✕
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @empty
                    <div class="bg-white rounded-lg py-16 text-center" style="border: 1px solid var(--color-border)">
                        <p class="text-xs mb-1" style="color: var(--color-muted)">No hay plantillas creadas aún</p>
                        <p class="text-[11px]" style="color: var(--color-subtle)">
                            Crea la primera plantilla usando el formulario de la izquierda
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif

</div>
