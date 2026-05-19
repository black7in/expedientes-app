<div @if($documento->isPendiente()) wire:poll.3000ms="refrescar" @endif>

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-1.5 text-xs mb-5" style="color: var(--color-muted)">
        <a href="{{ route('documentos.index') }}" wire:navigate class="hover:underline" style="color: var(--color-muted)">Documentos</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span style="color: var(--color-text-secondary)" class="truncate max-w-xs">{{ $documento->nombre_archivo }}</span>
    </div>

    {{-- Banner procesando (solo cuando está pendiente) --}}
    @if($documento->isPendiente())
        <div class="flex items-center gap-3 px-4 py-3 rounded-lg mb-5 text-sm"
             style="background-color: #fffbeb; border: 1px solid #fde68a; color: #92400e">
            <svg class="w-4 h-4 animate-spin flex-shrink-0" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
            </svg>
            <span class="font-medium">Extrayendo texto del documento...</span>
            <span class="text-xs opacity-70">Esta página se actualiza automáticamente</span>
        </div>
    @endif

    {{-- Banner indexación --}}
    @if($mensajeIndex)
        <div class="flex items-center gap-3 px-4 py-3 rounded-lg mb-4 text-xs"
             style="{{ $indexExito ? 'background-color:#f0fdf4;border:1px solid #bbf7d0;color:#15803d' : 'background-color:#fef2f2;border:1px solid #fecaca;color:#b91c1c' }}">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                @if($indexExito)
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                @else
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                @endif
            </svg>
            {{ $mensajeIndex }}
        </div>
    @endif

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4 mb-6">
        <div class="flex items-center gap-3 min-w-0">
            {{-- Format badge --}}
            <div class="w-8 h-8 rounded-md flex items-center justify-center flex-shrink-0 text-xs font-bold"
                 style="{{ $documento->formato === 'pdf' ? 'background-color:#fee2e2;color:#b91c1c' : 'background-color:#dbeafe;color:#1d4ed8' }}">
                {{ strtoupper($documento->formato) }}
            </div>
            <div class="min-w-0">
                <h2 class="text-sm font-semibold font-mono truncate" style="color: var(--color-text)">
                    {{ $documento->nombre_archivo }}
                </h2>
                <div class="flex items-center gap-2 mt-0.5">
                    @php
                        $estadoBadge = match($documento->estado_extraccion) {
                            'procesado' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                            'error'     => 'bg-red-50 text-red-700 ring-red-600/20',
                            default     => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                        };
                    @endphp
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium ring-1 ring-inset {{ $estadoBadge }}">
                        {{ ucfirst($documento->estado_extraccion) }}
                    </span>
                    <span class="text-[11px] capitalize" style="color: var(--color-muted)">{{ $documento->tipo_documento }}</span>
                </div>
            </div>
        </div>
        @if($documento->isProcesado())
            <button wire:click="indexar"
                    wire:loading.attr="disabled"
                    wire:target="indexar"
                    class="inline-flex items-center gap-1.5 h-8 px-3 text-xs font-medium rounded-md transition-opacity hover:opacity-80 disabled:opacity-50 flex-shrink-0"
                    style="border: 1px solid var(--color-border); color: var(--color-text)">
                <svg wire:loading.remove wire:target="indexar"
                     class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <svg wire:loading wire:target="indexar"
                     class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
                <span wire:loading.remove wire:target="indexar">
                    {{ $documento->indexado ? 'Re-indexar' : 'Indexar para RAG' }}
                </span>
                <span wire:loading wire:target="indexar">Indexando...</span>
            </button>
        @endif
    </div>

    {{-- Meta row --}}
    <div class="flex flex-wrap items-center gap-x-5 gap-y-1.5 text-xs mb-6" style="color: var(--color-muted)">
        @if($documento->usuario)
            <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                {{ $documento->usuario->nombre }}
            </span>
            <span style="color: var(--color-border-strong)">|</span>
        @endif
        <span class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            {{ $documento->created_at->format('d/m/Y H:i') }}
        </span>
        @if($documento->expediente)
            <span style="color: var(--color-border-strong)">|</span>
            <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Expediente:
                <a href="{{ route('expedientes.show', $documento->expediente) }}" wire:navigate
                   class="hover:underline font-mono" style="color: var(--color-primary)">
                    {{ $documento->expediente->numero_expediente ?? 'Sin número' }}
                </a>
            </span>
        @endif
    </div>

    {{-- Side-by-side layout (PB-6) --}}
    <div class="flex gap-5 items-start">

        {{-- LEFT — Metadata / info --}}
        <div class="w-72 flex-shrink-0 space-y-4">

            {{-- Estado de extracción --}}
            <div class="bg-white rounded-lg p-4" style="border: 1px solid var(--color-border)">
                <h3 class="text-xs font-semibold uppercase tracking-wide mb-3" style="color: var(--color-muted)">
                    Extracción NLP
                </h3>
                @if($documento->isProcesado() && $documento->texto_extraido)
                    <div class="space-y-2">
                        @foreach($documento->texto_extraido as $campo => $valor)
                            @if($valor)
                                <div>
                                    <p class="text-[10px] font-semibold uppercase tracking-wide mb-0.5" style="color: var(--color-subtle)">
                                        {{ str_replace('_', ' ', $campo) }}
                                    </p>
                                    <p class="text-xs leading-relaxed" style="color: var(--color-text)">
                                        @if(is_array($valor))
                                            {{ implode(', ', $valor) }}
                                        @else
                                            {{ $valor }}
                                        @endif
                                    </p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @elseif($documento->isPendiente())
                    <div class="flex items-center gap-2 py-2">
                        <div class="w-5 h-5 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-amber-700">Pendiente de procesamiento</p>
                            <p class="text-[11px] mt-0.5" style="color: var(--color-muted)">
                                El servicio de extracción procesará este documento en breve.
                            </p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-2 py-2">
                        <div class="w-5 h-5 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-red-700">Error en la extracción</p>
                            <p class="text-[11px] mt-0.5" style="color: var(--color-muted)">
                                Hubo un problema al procesar el documento.
                            </p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Detalles --}}
            <div class="bg-white rounded-lg p-4" style="border: 1px solid var(--color-border)">
                <h3 class="text-xs font-semibold uppercase tracking-wide mb-3" style="color: var(--color-muted)">Detalles</h3>
                <dl class="space-y-2.5">
                    <div>
                        <dt class="text-[10px] font-semibold uppercase tracking-wide" style="color: var(--color-subtle)">Formato</dt>
                        <dd class="text-xs mt-0.5" style="color: var(--color-text)">{{ strtoupper($documento->formato) }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-semibold uppercase tracking-wide" style="color: var(--color-subtle)">Tipo de documento</dt>
                        <dd class="text-xs mt-0.5 capitalize" style="color: var(--color-text)">{{ $documento->tipo_documento }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-semibold uppercase tracking-wide" style="color: var(--color-subtle)">Subido por</dt>
                        <dd class="text-xs mt-0.5" style="color: var(--color-text)">{{ $documento->usuario->nombre ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-semibold uppercase tracking-wide" style="color: var(--color-subtle)">Fecha de carga</dt>
                        <dd class="text-xs mt-0.5" style="color: var(--color-text)">{{ $documento->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @if($documento->expediente)
                        <div>
                            <dt class="text-[10px] font-semibold uppercase tracking-wide" style="color: var(--color-subtle)">Expediente</dt>
                            <dd class="text-xs mt-0.5">
                                <a href="{{ route('expedientes.show', $documento->expediente) }}" wire:navigate
                                   class="font-mono hover:underline" style="color: var(--color-primary)">
                                    {{ $documento->expediente->numero_expediente ?? 'Sin número' }}
                                </a>
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

        </div>

        {{-- RIGHT — Extracted text content --}}
        <div class="flex-1 min-w-0">
            <div class="bg-white rounded-lg" style="border: 1px solid var(--color-border)">
                <div class="flex items-center justify-between px-5 py-3.5" style="border-bottom: 1px solid var(--color-border)">
                    <h3 class="text-xs font-semibold uppercase tracking-wide" style="color: var(--color-muted)">Texto extraído</h3>
                    @if($documento->isProcesado())
                        <span class="inline-flex items-center gap-1 text-[11px]" style="color: var(--color-primary)">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Procesado
                        </span>
                    @endif
                </div>

                <div class="px-5 py-4">
                    @if($documento->isProcesado() && $documento->texto_extraido)
                        @php $texto = $documento->texto_extraido; @endphp
                        @if(isset($texto['texto_completo']))
                            <div class="prose prose-xs max-w-none">
                                <pre class="text-xs leading-relaxed whitespace-pre-wrap font-sans"
                                     style="color: var(--color-text); background: transparent; padding: 0; margin: 0">{{ $texto['texto_completo'] }}</pre>
                            </div>
                        @else
                            <pre class="text-xs leading-relaxed whitespace-pre-wrap font-mono"
                                 style="color: var(--color-text-secondary)">{{ json_encode($texto, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        @endif
                    @elseif($documento->isPendiente())
                        <div class="py-16 text-center">
                            <svg class="w-10 h-10 mx-auto mb-3 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-xs font-medium text-amber-700 mb-1">Extracción pendiente</p>
                            <p class="text-[11px]" style="color: var(--color-muted)">
                                El servicio FastAPI procesará este documento y extraerá su contenido.
                            </p>
                        </div>
                    @else
                        <div class="py-16 text-center">
                            <svg class="w-10 h-10 mx-auto mb-3 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-xs font-medium text-red-700 mb-1">Error al procesar el documento</p>
                            <p class="text-[11px]" style="color: var(--color-muted)">
                                No se pudo extraer el texto de este archivo.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

</div>
