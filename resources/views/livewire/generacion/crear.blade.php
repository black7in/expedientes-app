<div x-data="{}"
     x-on:descargar-docx.window="
        const a = document.createElement('a');
        a.href = 'data:application/vnd.openxmlformats-officedocument.wordprocessingml.document;base64,' + $event.detail.b64;
        a.download = $event.detail.nombre;
        a.click();
     ">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-1.5 text-xs mb-5" style="color: var(--color-muted)">
        <a href="{{ route('dashboard') }}" wire:navigate class="hover:underline" style="color: var(--color-muted)">Inicio</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <a href="{{ route('generacion.index') }}" wire:navigate class="hover:underline" style="color: var(--color-muted)">Generaciones</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span style="color: var(--color-text-secondary)">Nuevo documento</span>
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

            {{-- Expediente vinculado (modo A) --}}
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

            {{-- Plantilla --}}
            <div class="bg-white rounded-lg p-4" style="border: 1px solid var(--color-border)">
                <label class="block text-[10px] font-semibold uppercase tracking-wide mb-2" style="color: var(--color-subtle)">
                    Plantilla *
                </label>
                @if(empty($plantillas))
                    <p class="text-xs" style="color: var(--color-muted)">No hay plantillas disponibles (servicio IA no disponible)</p>
                @else
                    <select wire:model="plantilla_id"
                            class="w-full text-xs rounded-md px-3 py-2 focus:outline-none"
                            style="border: 1px solid var(--color-border); color: var(--color-text); background: white;">
                        @foreach($plantillas as $plt)
                            <option value="{{ $plt['id'] }}">
                                {{ $plt['nombre'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('plantilla_id')
                        <p class="text-[11px] mt-1 text-red-600">{{ $message }}</p>
                    @enderror
                @endif

                {{-- Formato de salida --}}
                <p class="text-[10px] font-semibold uppercase tracking-wide mt-3 mb-2" style="color: var(--color-subtle)">
                    Formato
                </p>
                <div class="flex rounded-md overflow-hidden" style="border: 1px solid var(--color-border)">
                    <button wire:click="$set('formato_salida', 'estructurado')" type="button"
                            class="flex-1 py-1.5 text-xs font-medium transition-colors"
                            style="{{ $formato_salida === 'estructurado' ? 'background-color: var(--color-primary); color: white;' : 'color: var(--color-muted);' }}">
                        Estructurado
                    </button>
                    <button wire:click="$set('formato_salida', 'corrido')" type="button"
                            class="flex-1 py-1.5 text-xs font-medium transition-colors"
                            style="{{ $formato_salida === 'corrido' ? 'background-color: var(--color-primary); color: white;' : 'color: var(--color-muted); border-left: 1px solid var(--color-border);' }}">
                        Corrido
                    </button>
                </div>
            </div>

            {{-- Datos del caso --}}
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
                    <label class="block text-[11px] mb-1" style="color: var(--color-muted)">Tipo de proceso</label>
                    <input type="text" wire:model="tipo_proceso" placeholder="Ej: Proceso Ordinario"
                           class="w-full text-xs rounded-md px-3 py-2 focus:outline-none"
                           style="border: 1px solid var(--color-border); color: var(--color-text); background: white;">
                </div>

                <div>
                    <label class="block text-[11px] mb-1" style="color: var(--color-muted)">Hechos del caso *</label>
                    <textarea wire:model="hechos" rows="6"
                              placeholder="Describe los hechos del caso de forma clara y cronológica (mínimo 50 caracteres)..."
                              class="w-full text-xs rounded-md px-3 py-2 focus:outline-none resize-none"
                              style="border: 1px solid var(--color-border); color: var(--color-text); background: white;"></textarea>
                    @error('hechos')
                        <p class="text-[11px] mt-0.5 text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Instrucciones adicionales --}}
            <div class="bg-white rounded-lg p-4" style="border: 1px solid var(--color-border)">
                <label class="block text-[10px] font-semibold uppercase tracking-wide mb-2" style="color: var(--color-subtle)">
                    Instrucciones adicionales
                </label>
                <textarea wire:model="instrucciones_extra" rows="3"
                          placeholder="Ej: enfatizar daños y perjuicios, incluir medida cautelar..."
                          class="w-full text-xs rounded-md px-3 py-2 focus:outline-none resize-none"
                          style="border: 1px solid var(--color-border); color: var(--color-text); background: white;"></textarea>
            </div>

            {{-- Botón generar --}}
            <button wire:click="generar"
                    wire:loading.attr="disabled"
                    wire:target="generar"
                    @if(empty($plantillas)) disabled @endif
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
                <div class="flex items-start gap-3 px-4 py-3 rounded-lg mb-4"
                     style="background-color: #fef2f2; border: 1px solid #fecaca; color: #b91c1c">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-xs">{{ $error }}</span>
                </div>
            @endif

            {{-- Estado vacío / cargando --}}
            <div class="bg-white rounded-lg flex flex-col items-center justify-center py-24"
                 style="border: 1px solid var(--color-border)">
                @if($generando)
                    <svg class="w-8 h-8 mb-3 animate-spin" fill="none" viewBox="0 0 24 24"
                         style="color: var(--color-primary)">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                    </svg>
                    <p class="text-sm font-medium" style="color: var(--color-text)">Generando sección por sección...</p>
                    <p class="text-xs mt-1" style="color: var(--color-muted)">
                        Claude está redactando el documento. Esto puede tardar entre 30 y 90 segundos.
                    </p>
                @else
                    <svg class="w-10 h-10 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                         style="color: var(--color-border-strong)">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p class="text-sm font-medium" style="color: var(--color-text-secondary)">El documento aparecerá aquí</p>
                    <p class="text-xs mt-1" style="color: var(--color-muted)">
                        Completa el formulario y haz clic en "Generar documento"
                    </p>
                @endif
            </div>

        </div>

    </div>

</div>
