<div x-data="{}"
     x-on:copiar-al-portapapeles.window="
        navigator.clipboard.writeText($event.detail.texto);
     ">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-1.5 text-xs mb-5" style="color: var(--color-muted)">
        <a href="{{ route('dashboard') }}" wire:navigate class="hover:underline" style="color: var(--color-muted)">Inicio</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span style="color: var(--color-text-secondary)">Generar Documento</span>
    </div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-lg font-semibold" style="color: var(--color-text)">Generar Documento</h1>
            <p class="text-xs mt-0.5" style="color: var(--color-muted)">
                Redacción asistida por IA con base en legislación boliviana y jurisprudencia del TSJ
            </p>
        </div>
    </div>

    <div class="flex gap-5 items-start">

        {{-- ── PANEL IZQUIERDO — Formulario ──────────────────────────────────────── --}}
        <div class="w-80 flex-shrink-0 space-y-4">

            {{-- Selector de modo (solo si no viene de un expediente) --}}
            @if(!$expediente)
                <div class="bg-white rounded-lg p-4" style="border: 1px solid var(--color-border)">
                    <p class="text-[10px] font-semibold uppercase tracking-wide mb-2.5" style="color: var(--color-subtle)">Fuente del contexto</p>
                    <div class="flex rounded-md overflow-hidden" style="border: 1px solid var(--color-border)">
                        <button wire:click="$set('modo', 'manual')"
                                class="flex-1 py-1.5 text-xs font-medium transition-colors"
                                style="{{ $modo === 'manual' ? 'background-color: var(--color-primary); color: white;' : 'color: var(--color-muted);' }}">
                            Formulario manual
                        </button>
                        <button wire:click="$set('modo', 'expediente')"
                                class="flex-1 py-1.5 text-xs font-medium transition-colors"
                                style="{{ $modo === 'expediente' ? 'background-color: var(--color-primary); color: white;' : 'color: var(--color-muted); border-left: 1px solid var(--color-border);' }}">
                            Desde expediente
                        </button>
                    </div>
                </div>
            @endif

            {{-- Contexto del expediente (modo A) --}}
            @if($expediente)
                <div class="bg-white rounded-lg p-4" style="border: 1px solid var(--color-border)">
                    <p class="text-[10px] font-semibold uppercase tracking-wide mb-2.5" style="color: var(--color-subtle)">Expediente vinculado</p>
                    <p class="text-xs font-mono font-semibold" style="color: var(--color-text)">
                        {{ $expediente->numero_expediente ?? 'Sin número' }}
                    </p>
                    @if($expediente->tipoProceso)
                        <p class="text-[11px] mt-1" style="color: var(--color-muted)">{{ $expediente->tipoProceso->nombre }}</p>
                    @endif
                    @if($expediente->juzgado)
                        <p class="text-[11px]" style="color: var(--color-muted)">{{ $expediente->juzgado->nombre }}</p>
                    @endif
                    <a href="{{ route('expedientes.show', $expediente) }}" wire:navigate
                       class="inline-block text-[11px] mt-2 hover:underline" style="color: var(--color-primary)">
                        Ver expediente →
                    </a>
                </div>
            @endif

            {{-- Tipo de documento --}}
            <div class="bg-white rounded-lg p-4" style="border: 1px solid var(--color-border)">
                <label class="block text-[10px] font-semibold uppercase tracking-wide mb-2" style="color: var(--color-subtle)">
                    Tipo de documento *
                </label>
                <select wire:model="tipo_documento"
                        class="w-full text-xs rounded-md px-3 py-2 focus:outline-none"
                        style="border: 1px solid var(--color-border); color: var(--color-text); background: white;">
                    <option value="demanda">Demanda</option>
                    <option value="memorial">Memorial de trámite</option>
                    <option value="contestacion">Contestación de demanda</option>
                    <option value="apelacion">Recurso de apelación</option>
                    <option value="nulidad">Recurso de nulidad</option>
                    <option value="contrato">Contrato civil</option>
                </select>
                @error('tipo_documento')
                    <p class="text-[11px] mt-1 text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Campos modo manual (B) --}}
            @if($modo === 'manual')
                <div class="bg-white rounded-lg p-4 space-y-3" style="border: 1px solid var(--color-border)">
                    <p class="text-[10px] font-semibold uppercase tracking-wide" style="color: var(--color-subtle)">Datos del caso</p>

                    <div>
                        <label class="block text-[11px] mb-1" style="color: var(--color-muted)">Demandante *</label>
                        <input type="text" wire:model="demandante" placeholder="Nombre completo"
                               class="w-full text-xs rounded-md px-3 py-2 focus:outline-none"
                               style="border: 1px solid var(--color-border); color: var(--color-text); background: white;">
                        @error('demandante')
                            <p class="text-[11px] mt-0.5 text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] mb-1" style="color: var(--color-muted)">Demandado *</label>
                        <input type="text" wire:model="demandado" placeholder="Nombre completo"
                               class="w-full text-xs rounded-md px-3 py-2 focus:outline-none"
                               style="border: 1px solid var(--color-border); color: var(--color-text); background: white;">
                        @error('demandado')
                            <p class="text-[11px] mt-0.5 text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] mb-1" style="color: var(--color-muted)">Juzgado</label>
                        <input type="text" wire:model="juzgado" placeholder="Ej: Juzgado 1ro Civil"
                               class="w-full text-xs rounded-md px-3 py-2 focus:outline-none"
                               style="border: 1px solid var(--color-border); color: var(--color-text); background: white;">
                    </div>

                    <div>
                        <label class="block text-[11px] mb-1" style="color: var(--color-muted)">Ciudad</label>
                        <input type="text" wire:model="ciudad"
                               class="w-full text-xs rounded-md px-3 py-2 focus:outline-none"
                               style="border: 1px solid var(--color-border); color: var(--color-text); background: white;">
                    </div>

                    <div>
                        <label class="block text-[11px] mb-1" style="color: var(--color-muted)">Tipo de proceso *</label>
                        <input type="text" wire:model="tipo_proceso" placeholder="Ej: Proceso Ordinario"
                               class="w-full text-xs rounded-md px-3 py-2 focus:outline-none"
                               style="border: 1px solid var(--color-border); color: var(--color-text); background: white;">
                        @error('tipo_proceso')
                            <p class="text-[11px] mt-0.5 text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-[11px] mb-1" style="color: var(--color-muted)">Hechos del caso *</label>
                        <textarea wire:model="hechos" rows="5" placeholder="Describe los hechos del caso de forma clara y cronológica..."
                                  class="w-full text-xs rounded-md px-3 py-2 focus:outline-none resize-none"
                                  style="border: 1px solid var(--color-border); color: var(--color-text); background: white;"></textarea>
                        @error('hechos')
                            <p class="text-[11px] mt-0.5 text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            @endif

            {{-- Instrucciones adicionales --}}
            <div class="bg-white rounded-lg p-4" style="border: 1px solid var(--color-border)">
                <label class="block text-[10px] font-semibold uppercase tracking-wide mb-2" style="color: var(--color-subtle)">
                    Instrucciones adicionales
                </label>
                <textarea wire:model="instrucciones" rows="3"
                          placeholder="Ej: enfatizar daños y perjuicios, incluir medida cautelar..."
                          class="w-full text-xs rounded-md px-3 py-2 focus:outline-none resize-none"
                          style="border: 1px solid var(--color-border); color: var(--color-text); background: white;"></textarea>
            </div>

            {{-- Botón generar --}}
            <button wire:click="generar"
                    wire:loading.attr="disabled"
                    wire:target="generar"
                    class="w-full flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm font-medium transition-opacity disabled:opacity-60"
                    style="background-color: var(--color-primary); color: white;">
                <span wire:loading.remove wire:target="generar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </span>
                <svg wire:loading wire:target="generar"
                     class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                </svg>
                <span wire:loading.remove wire:target="generar">Generar documento</span>
                <span wire:loading wire:target="generar">Generando con IA...</span>
            </button>

        </div>

        {{-- ── PANEL DERECHO — Resultado ──────────────────────────────────────────── --}}
        <div class="flex-1 min-w-0">

            {{-- Error --}}
            @if($error)
                <div class="flex items-start gap-3 px-4 py-3 rounded-lg mb-4 text-sm"
                     style="background-color: #fef2f2; border: 1px solid #fecaca; color: #b91c1c">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-xs">{{ $error }}</span>
                </div>
            @endif

            {{-- Estado vacío / cargando --}}
            @if(!$borrador)
                <div class="bg-white rounded-lg flex flex-col items-center justify-center py-24"
                     style="border: 1px solid var(--color-border)">
                    @if($generando)
                        <svg class="w-8 h-8 mb-3 animate-spin" fill="none" viewBox="0 0 24 24"
                             style="color: var(--color-primary)">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                        </svg>
                        <p class="text-sm font-medium" style="color: var(--color-text)">Generando documento...</p>
                        <p class="text-xs mt-1" style="color: var(--color-muted)">
                            Claude está redactando el borrador. Esto puede tardar unos segundos.
                        </p>
                    @else
                        <svg class="w-10 h-10 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color: var(--color-border-strong)">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm font-medium" style="color: var(--color-text-secondary)">El borrador aparecerá aquí</p>
                        <p class="text-xs mt-1" style="color: var(--color-muted)">
                            Completa el formulario y haz clic en "Generar documento"
                        </p>
                    @endif
                </div>
            @else
                {{-- Resultado --}}
                <div class="bg-white rounded-lg" style="border: 1px solid var(--color-border)">

                    {{-- Header del resultado --}}
                    <div class="flex items-center justify-between px-5 py-3.5"
                         style="border-bottom: 1px solid var(--color-border)">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-700">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Documento generado
                            </span>
                            @if($tokens_usados)
                                <span class="text-[11px]" style="color: var(--color-muted)">
                                    · {{ number_format($tokens_usados) }} tokens
                                    · {{ round($tiempo_ms / 1000, 1) }}s
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <button wire:click="copiarBorrador"
                                    class="flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-md transition-colors hover:opacity-80"
                                    style="border: 1px solid var(--color-border); color: var(--color-muted);">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                Copiar
                            </button>
                            <button wire:click="nuevaGeneracion"
                                    class="flex items-center gap-1.5 text-xs px-3 py-1.5 rounded-md transition-colors hover:opacity-80"
                                    style="border: 1px solid var(--color-border); color: var(--color-muted);">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Nueva generación
                            </button>
                        </div>
                    </div>

                    {{-- Borrador --}}
                    <div class="px-5 py-4">
                        <pre class="text-xs leading-relaxed whitespace-pre-wrap font-sans"
                             style="color: var(--color-text)">{{ $borrador }}</pre>
                    </div>

                </div>
            @endif

        </div>

    </div>

</div>
