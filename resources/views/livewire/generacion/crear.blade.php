<div>

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
        <span style="color: var(--color-text-secondary)">
            @if($estado === 'editor') Resultado
            @elseif($estado === 'procesando') Generando...
            @else Nuevo documento
            @endif
        </span>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- ESTADO: INPUT                                                          --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if($estado === 'input')

        <div class="flex gap-5" style="height: calc(100vh - 140px); overflow: hidden">

            {{-- ── Panel izquierdo — contexto ──────────────────────────────── --}}
            <div class="w-64 flex-shrink-0 space-y-4 overflow-y-auto min-h-0 pr-1">

                {{-- Título --}}
                <div>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3"
                         style="background: var(--color-sidebar-active)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color: var(--color-primary)">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <h1 class="text-sm font-semibold" style="color: var(--color-text)">Asistente de redacción</h1>
                    <p class="text-xs mt-1 leading-relaxed" style="color: var(--color-muted)">
                        Describí el caso en lenguaje natural y la IA genera el memorial jurídico en boliviano.
                    </p>
                </div>

                {{-- Reglas --}}
                <div class="bg-white rounded-lg p-4" style="border: 1px solid var(--color-border)">
                    <p class="text-[10px] font-semibold uppercase tracking-wide mb-3" style="color: var(--color-subtle)">
                        Qué escribir
                    </p>
                    <ul class="space-y-3">
                        @foreach([
                            ['1', 'Indicá el tipo de acción', 'ejecutiva, coactiva, ordinaria, desalojo...'],
                            ['2', 'Nombrá a todas las partes', 'nombre completo + CI de cada una'],
                            ['3', 'Fechas exactas', 'del contrato, vencimiento, incumplimiento'],
                            ['4', 'Montos en cifras', 'Bs. o USD, capital e intereses si aplica'],
                            ['5', 'Hechos en orden', 'qué pasó primero, qué pasó después'],
                            ['6', 'Pretensión concreta', 'qué pedís al juzgado exactamente'],
                            ['7', 'Jurisprudencia', 'si el caso lo requiere, activá el toggle — incluye Autos Supremos relevantes pero tarda más'],
                        ] as [$num, $titulo, $detalle])
                            <li class="flex items-start gap-2.5">
                                <span class="text-[10px] font-bold w-4 flex-shrink-0 mt-0.5"
                                      style="color: var(--color-primary)">{{ $num }}.</span>
                                <div>
                                    <p class="text-[11px] font-medium" style="color: var(--color-text)">{{ $titulo }}</p>
                                    <p class="text-[11px]" style="color: var(--color-muted)">{{ $detalle }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Error --}}
                @if($errorMsg)
                    <div class="rounded-lg px-3 py-2.5 text-xs"
                         style="background:#fef2f2; border:1px solid #fecaca; color:#b91c1c">
                        {{ $errorMsg }}
                    </div>
                @endif

            </div>

            {{-- ── Panel derecho — formulario ──────────────────────────────── --}}
            <div class="flex-1 min-w-0 min-h-0 flex flex-col gap-3">

                {{-- Textarea —  ocupa todo el espacio disponible --}}
                <div class="flex-1 min-h-0 bg-white rounded-xl flex flex-col"
                     style="border: 1px solid var(--color-border)">
                    <div class="px-5 pt-4 pb-2 flex-shrink-0"
                         style="border-bottom: 1px solid var(--color-border)">
                        <p class="text-xs font-medium" style="color: var(--color-text)">Descripción del caso</p>
                    </div>
                    <textarea wire:model="narracion"
                              placeholder="Ej.: Mi cliente Juan Pérez, CI 1234567, prestó Bs. 50.000 al señor Mario López el 15 de enero de 2024, plazo 6 meses sin intereses, mediante documento privado. Al vencimiento en julio de 2024 el deudor no pagó. Se pretende acción ejecutiva ante el Juez de Partido Civil de Cochabamba..."
                              class="flex-1 w-full px-5 py-4 resize-none focus:outline-none text-xs leading-relaxed"
                              style="color: var(--color-text); background: transparent"></textarea>
                </div>

                {{-- Barra inferior: contador + toggle + botón --}}
                <div class="flex-shrink-0 bg-white rounded-xl px-4 py-3 flex items-center gap-4"
                     style="border: 1px solid var(--color-border)">

                    {{-- Contador --}}
                    <span class="text-[11px] flex-shrink-0" style="color: var(--color-muted)">
                        {{ strlen($narracion) }} caracteres
                        @if(strlen($narracion) > 0 && strlen($narracion) < 100)
                            <span style="color: #d97706"> · mínimo 100</span>
                        @endif
                    </span>

                    @error('narracion')
                        <span class="text-[11px]" style="color: #dc2626">{{ $message }}</span>
                    @enderror

                    <div class="flex-1"></div>

                    {{-- Toggle jurisprudencia --}}
                    <label class="flex items-center gap-2 cursor-pointer select-none flex-shrink-0"
                           x-data="{ on: $wire.entangle('incluirJuris') }">
                        <div class="relative">
                            <input type="checkbox" wire:model="incluirJuris" class="sr-only">
                            <div class="w-8 h-4 rounded-full transition-colors duration-200"
                                 :style="on ? 'background:var(--color-primary)' : 'background:var(--color-border-strong)'"></div>
                            <div class="absolute top-0.5 left-0.5 w-3 h-3 rounded-full bg-white shadow transition-transform duration-200"
                                 :style="on ? 'transform:translateX(16px)' : ''"></div>
                        </div>
                        <span class="text-xs" style="color: var(--color-text)">Incluir jurisprudencia</span>
                    </label>

                    {{-- Botón generar --}}
                    <button wire:click="generar"
                            wire:loading.attr="disabled"
                            wire:target="generar"
                            class="flex items-center gap-2 h-9 px-5 rounded-lg text-xs font-medium hover:opacity-90 disabled:opacity-60 flex-shrink-0"
                            style="background-color: var(--color-primary); color: white;">
                        <span wire:loading.remove wire:target="generar" class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Generar documento
                        </span>
                        <span wire:loading wire:target="generar" class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                            </svg>
                            Preparando...
                        </span>
                    </button>

                </div>

            </div>

        </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- ESTADO: PROCESANDO                                                     --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @elseif($estado === 'procesando')

        <div wire:poll.2s="verificarEstado"
             class="flex gap-5" style="height: calc(100vh - 140px); overflow: hidden">

            {{-- ── Panel izquierdo — timeline ──────────────────────────────── --}}
            <div class="w-64 flex-shrink-0 min-h-0 pr-1 flex flex-col">

                @php
                    $pasos = [
                        ['id' => 'analizando',  'label' => 'Analizando los hechos',  'desc' => 'Extrae inventario, partes y pretensión'],
                        ['id' => 'recuperando', 'label' => 'Recuperando normativa',  'desc' => 'Busca artículos y moldes aplicables'],
                        ['id' => 'generando',   'label' => 'Redactando el documento','desc' => 'El LLM produce el memorial'],
                    ];
                    $orden     = array_column($pasos, 'id');
                    $idx       = array_search($pasoActual, $orden);
                    $idx       = $idx === false ? 0 : $idx;
                @endphp

                {{-- Encabezado --}}
                <div class="mb-7">
                    <p class="text-sm font-semibold" style="color: var(--color-text)">Procesando</p>
                    <p class="text-xs mt-0.5" style="color: var(--color-muted)">30 – 90 segundos</p>
                </div>

                {{-- Timeline vertical --}}
                <div>
                    @foreach($pasos as $i => $paso)
                        @php
                            $done   = $i < $idx;
                            $active = $i === $idx;
                        @endphp

                        <div class="flex items-stretch gap-4">

                            {{-- Columna izquierda: nodo + conector --}}
                            <div class="flex flex-col items-center flex-shrink-0" style="width: 36px">

                                {{-- Nodo --}}
                                <div class="relative flex-shrink-0">
                                    @if($active)
                                        <span class="absolute inset-0 rounded-full animate-ping"
                                              style="background: var(--color-primary); opacity: 0.25"></span>
                                    @endif
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center relative z-10"
                                         style="background: {{ $done ? '#059669' : ($active ? 'var(--color-primary)' : 'var(--color-border)') }}">
                                        @if($done)
                                            <svg class="w-4 h-4" fill="none" stroke="white" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        @elseif($active)
                                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-30" cx="12" cy="12" r="10" stroke="white" stroke-width="4"/>
                                                <path class="opacity-90" fill="white" d="M4 12a8 8 0 018-8v8H4z"/>
                                            </svg>
                                        @else
                                            <span class="w-2.5 h-2.5 rounded-full bg-white opacity-40"></span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Conector vertical (no en el último) --}}
                                @if(!$loop->last)
                                    <div class="w-0.5 my-1" style="flex: 1; min-height: 40px;
                                                background: {{ $done ? '#059669' : 'var(--color-border)' }}"></div>
                                @endif

                            </div>

                            {{-- Columna derecha: texto --}}
                            <div class="{{ $loop->last ? 'pt-2' : 'pb-10 pt-2' }}">
                                <p class="text-xs leading-tight"
                                   style="color: {{ $done ? '#059669' : ($active ? 'var(--color-text)' : 'var(--color-muted)') }};
                                          font-weight: {{ $active ? '600' : '400' }}">
                                    {{ $paso['label'] }}
                                </p>
                                @if($active)
                                    <p class="text-[11px] mt-1 leading-relaxed" style="color: var(--color-muted)">
                                        {{ $paso['desc'] }}
                                    </p>
                                @endif
                            </div>

                        </div>
                    @endforeach
                </div>

            </div>

            {{-- ── Panel derecho — narración enviada ───────────────────────── --}}
            <div class="flex-1 min-w-0 min-h-0 bg-white rounded-xl flex flex-col"
                 style="border: 1px solid var(--color-border)">

                <div class="px-6 py-3 flex-shrink-0"
                     style="border-bottom: 1px solid var(--color-border)">
                    <p class="text-xs font-medium" style="color: var(--color-text-secondary)">Caso enviado a procesar</p>
                </div>

                <div class="flex-1 overflow-y-auto px-6 py-5 min-h-0">
                    <p class="text-xs leading-relaxed whitespace-pre-wrap" style="color: var(--color-text)">{{ $narracion }}</p>
                </div>

            </div>

        </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- ESTADO: EDITOR                                                         --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @else

        <div class="flex gap-5" style="height: calc(100vh - 140px); overflow: hidden">

            {{-- ── Panel izquierdo ─────────────────────────────────────────── --}}
            <div class="w-60 flex-shrink-0 space-y-3 overflow-y-auto min-h-0 pr-1">

                <button wire:click="guardar"
                        wire:loading.attr="disabled"
                        wire:target="guardar"
                        class="w-full flex items-center justify-center gap-2 h-9 rounded-lg text-xs font-medium hover:opacity-90 disabled:opacity-60"
                        style="background-color: var(--color-primary); color: white;">
                    <span wire:loading.remove wire:target="guardar">Guardar cambios</span>
                    <span wire:loading wire:target="guardar">Guardando...</span>
                </button>

                @if($generacionId)
                    <a href="{{ route('generacion.descargar', $generacionId) }}" target="_blank"
                       class="w-full flex items-center justify-center gap-2 h-9 rounded-lg text-xs font-medium hover:opacity-80"
                       style="border: 1px solid var(--color-border); color: var(--color-muted);">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Exportar a Word
                    </a>
                @endif

                <button wire:click="nuevaGeneracion"
                        class="w-full flex items-center justify-center h-8 rounded-lg text-xs hover:opacity-80"
                        style="color: var(--color-muted)">
                    + Nueva generación
                </button>

                {{-- Revisiones --}}
                @php
                    $revisiones = array_merge(
                        array_map(fn($a) => str_replace('_', ' ', $a), $advertencias),
                        array_map(fn($v) => $v['detalle'] ?? '', $validaciones)
                    );
                    $revisiones = array_filter($revisiones);
                @endphp
                @if(!empty($revisiones))
                    <div class="rounded-lg p-3" style="background:#fffbeb; border:1px solid #fde68a">
                        <p class="text-[10px] font-semibold uppercase mb-2" style="color:#92400e">Revisiones pendientes</p>
                        @foreach($revisiones as $r)
                            <p class="text-[11px] leading-relaxed" style="color:#92400e">· {{ $r }}</p>
                        @endforeach
                    </div>
                @endif

                {{-- Calificación --}}
                @if($generacionId)
                    <div class="bg-white rounded-lg p-3" style="border: 1px solid var(--color-border)">
                        <p class="text-[10px] font-semibold uppercase mb-2" style="color: var(--color-subtle)">
                            ¿Qué tan útil fue?
                        </p>
                        <div class="flex gap-1">
                            @for($i = 1; $i <= 5; $i++)
                                <button wire:click="calificar({{ $i }})"
                                        class="text-xl leading-none transition-transform hover:scale-110"
                                        style="color: var(--color-border-strong)"
                                        title="{{ $i }} estrella{{ $i > 1 ? 's' : '' }}">★</button>
                            @endfor
                        </div>
                    </div>
                @endif

            </div>

            {{-- ── Panel derecho — TipTap ──────────────────────────────────── --}}
            <div class="flex-1 min-w-0 min-h-0 overflow-hidden">
                <div class="bg-white rounded-xl h-full flex flex-col" style="border: 1px solid var(--color-border)"
                     wire:ignore
                     x-data="tiptapEditor(@js($documentoHtml ?? ''), 'contenidoEditado')"
                     x-init="init()"
                     x-destroy="destroy()">

                    <div class="flex items-center gap-0.5 px-3 py-1.5 flex-wrap flex-shrink-0"
                         style="border-bottom: 1px solid var(--color-border); background:#fafafa; border-radius: 0.75rem 0.75rem 0 0">

                        <button type="button" @click="cmd('bold')" class="w-7 h-7 rounded flex items-center justify-center text-sm font-bold transition-colors"
                                :style="active('bold') ? 'background:var(--color-primary-light);color:var(--color-primary)' : 'color:var(--color-muted)'" title="Negrita">B</button>
                        <button type="button" @click="cmd('italic')" class="w-7 h-7 rounded flex items-center justify-center text-sm italic transition-colors"
                                :style="active('italic') ? 'background:var(--color-primary-light);color:var(--color-primary)' : 'color:var(--color-muted)'" title="Cursiva">I</button>
                        <button type="button" @click="cmd('underline')" class="w-7 h-7 rounded flex items-center justify-center text-sm underline transition-colors"
                                :style="active('underline') ? 'background:var(--color-primary-light);color:var(--color-primary)' : 'color:var(--color-muted)'" title="Subrayado">U</button>

                        <div class="w-px h-4 mx-1" style="background: var(--color-border)"></div>

                        <button type="button" @click="cmd('h1')" class="px-1.5 h-7 rounded flex items-center justify-center text-xs font-bold transition-colors"
                                :style="active('heading',{level:1}) ? 'background:var(--color-primary-light);color:var(--color-primary)' : 'color:var(--color-muted)'" title="Título 1">H1</button>
                        <button type="button" @click="cmd('h2')" class="px-1.5 h-7 rounded flex items-center justify-center text-xs font-semibold transition-colors"
                                :style="active('heading',{level:2}) ? 'background:var(--color-primary-light);color:var(--color-primary)' : 'color:var(--color-muted)'" title="Título 2">H2</button>
                        <button type="button" @click="cmd('h3')" class="px-1.5 h-7 rounded flex items-center justify-center text-xs transition-colors"
                                :style="active('heading',{level:3}) ? 'background:var(--color-primary-light);color:var(--color-primary)' : 'color:var(--color-muted)'" title="Título 3">H3</button>

                        <div class="w-px h-4 mx-1" style="background: var(--color-border)"></div>

                        <button type="button" @click="cmd('bulletList')" class="w-7 h-7 rounded flex items-center justify-center transition-colors"
                                :style="active('bulletList') ? 'background:var(--color-primary-light);color:var(--color-primary)' : 'color:var(--color-muted)'" title="Lista">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <button type="button" @click="cmd('orderedList')" class="w-7 h-7 rounded flex items-center justify-center transition-colors"
                                :style="active('orderedList') ? 'background:var(--color-primary-light);color:var(--color-primary)' : 'color:var(--color-muted)'" title="Lista numerada">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 6h1M4 10h1M4 14h1"/>
                            </svg>
                        </button>

                        <div class="w-px h-4 mx-1" style="background: var(--color-border)"></div>

                        <button type="button" @click="cmd('clear')" class="px-1.5 h-7 rounded text-[10px] transition-colors hover:opacity-80"
                                style="color: var(--color-muted)" title="Quitar formato">Limpiar</button>
                    </div>

                    <div x-ref="content" class="flex-1 overflow-y-auto px-8 py-6 min-h-0"></div>

                </div>
            </div>

        </div>

    @endif

</div>
