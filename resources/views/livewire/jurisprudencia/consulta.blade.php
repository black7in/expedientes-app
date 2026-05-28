<div
    x-data="{
        estado: 'idle',
        preguntaInput: '',
        validationError: '',
        pasoActual: '',
        pasosVistos: [],
        source: null,

        pasos: [
            { id: 'analizando',    label: 'Analizando la consulta',         desc: 'El LLM extrae filtros jurídicos y valida la pregunta',           opcional: false },
            { id: 'embedding',     label: 'Representación semántica',        desc: 'Vectorizando la consulta para búsqueda por similitud',           opcional: false },
            { id: 'busqueda_local',label: 'Búsqueda local',                  desc: 'Consultando resoluciones ya indexadas en la base de datos',      opcional: false },
            { id: 'genesis',       label: 'Consultando GÉNESIS del TSJ',     desc: 'Accediendo al repositorio oficial de resoluciones bolivianas',   opcional: true  },
            { id: 'busqueda_final',label: 'Refinando con nuevas resoluciones','desc': 'Re-clasificando con los fallos recién indexados',             opcional: true  },
            { id: 'generando',     label: 'Generando análisis jurídico',     desc: 'El LLM redacta la respuesta con citas a resoluciones relevantes',opcional: false },
        ],

        get pasosVisibles() {
            return this.pasos.filter(p => !p.opcional || this.pasosVistos.includes(p.id));
        },

        isPasoCompleto(pasoId) {
            const vis = this.pasosVisibles;
            const myIdx  = vis.findIndex(p => p.id === pasoId);
            const curIdx = vis.findIndex(p => p.id === this.pasoActual);
            return myIdx < curIdx || this.estado === 'resultado';
        },

        isPasoActivo(pasoId) {
            return this.pasoActual === pasoId && this.estado === 'buscando';
        },

        async buscar() {
            const pregunta = this.preguntaInput.trim();
            if (pregunta.length < 10) { this.validationError = 'Mínimo 10 caracteres.'; return; }
            if (pregunta.length > 500) { this.validationError = 'Máximo 500 caracteres.'; return; }
            this.validationError = '';
            this.estado     = 'buscando';
            this.pasoActual = '';
            this.pasosVistos = [];

            const url = '{{ route('jurisprudencia.stream') }}?pregunta=' + encodeURIComponent(pregunta);
            this.source = new EventSource(url);

            this.source.onmessage = async (e) => {
                const d = JSON.parse(e.data);

                if (d.tipo === 'progreso') {
                    if (!this.pasosVistos.includes(d.paso)) this.pasosVistos.push(d.paso);
                    this.pasoActual = d.paso;
                }

                if (d.tipo === 'resultado') {
                    this.source.close(); this.source = null;
                    this.pasoActual = 'completado';
                    if (!d.data.es_juridica) {
                        await $wire.recibirError(d.data.respuesta || 'La consulta no está relacionada con jurisprudencia boliviana.');
                        this.estado = 'error';
                    } else {
                        await $wire.recibirResultado(d.data);
                        this.estado = 'resultado';
                    }
                }

                if (d.tipo === 'error') {
                    this.source.close(); this.source = null;
                    await $wire.recibirError(d.mensaje || 'Error al procesar la consulta.');
                    this.estado = 'error';
                }
            };

            this.source.onerror = async () => {
                if (this.source) { this.source.close(); this.source = null; }
                if (this.estado === 'buscando') {
                    await $wire.recibirError('Error de conexión con el servicio de jurisprudencia.');
                    this.estado = 'error';
                }
            };
        },

        async nuevaConsulta() {
            if (this.source) { this.source.close(); this.source = null; }
            this.estado      = 'idle';
            this.preguntaInput = '';
            this.pasoActual  = '';
            this.pasosVistos = [];
            this.validationError = '';
            await $wire.call('nuevaConsulta');
        }
    }"
>

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-1.5 text-xs mb-5" style="color: var(--color-muted)">
        <a href="{{ route('dashboard') }}" wire:navigate class="hover:underline" style="color: var(--color-muted)">Inicio</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span style="color: var(--color-text-secondary)">Jurisprudencia TSJ</span>
    </div>

    <div class="flex gap-5" style="height: calc(100vh - 140px); overflow: hidden">

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- PANEL IZQUIERDO                                                   --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        <div class="w-64 flex-shrink-0 flex flex-col min-h-0">

            {{-- ── IDLE: título e instrucciones ──────────────────────────────── --}}
            <div x-show="estado === 'idle'" class="space-y-4 overflow-y-auto min-h-0 pr-1">

                <div>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-3"
                         style="background: var(--color-sidebar-active)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color: var(--color-primary)">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                        </svg>
                    </div>
                    <h1 class="text-sm font-semibold" style="color: var(--color-text)">Jurisprudencia TSJ Bolivia</h1>
                    <p class="text-xs mt-1 leading-relaxed" style="color: var(--color-muted)">
                        Consultá resoluciones del Tribunal Supremo de Justicia en lenguaje natural.
                        La IA busca, indexa y analiza los fallos relevantes a tu caso.
                    </p>
                </div>

                <div class="bg-white rounded-lg p-4" style="border: 1px solid var(--color-border)">
                    <p class="text-[10px] font-semibold uppercase tracking-wide mb-3" style="color: var(--color-subtle)">
                        Ejemplos de consultas
                    </p>
                    <ul class="space-y-2">
                        @foreach([
                            'Despido de trabajadora embarazada',
                            'Prescripción en materia civil',
                            'Nulidad de contrato por vicios del consentimiento',
                            'Derechos del imputado en proceso penal',
                            'Pensión de asistencia familiar por divorcio',
                        ] as $ejemplo)
                            <li>
                                <button type="button"
                                        @click="preguntaInput = '{{ $ejemplo }}'"
                                        class="text-left text-[11px] leading-snug w-full hover:underline"
                                        style="color: var(--color-primary)">
                                    {{ $ejemplo }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>

            {{-- ── BUSCANDO: línea de vida / progreso ────────────────────────── --}}
            <div x-show="estado === 'buscando'" class="flex flex-col min-h-0">

                <div class="mb-6">
                    <p class="text-sm font-semibold" style="color: var(--color-text)">Procesando</p>
                    <p class="text-xs mt-0.5" style="color: var(--color-muted)">Puede tardar 20–60 segundos</p>
                </div>

                <div class="flex-1 overflow-y-auto min-h-0">
                    <template x-for="(paso, i) in pasosVisibles" :key="paso.id">
                        <div class="flex items-stretch gap-3">

                            {{-- Columna: nodo + conector --}}
                            <div class="flex flex-col items-center flex-shrink-0" style="width: 32px">

                                {{-- Nodo --}}
                                <div class="relative flex-shrink-0">
                                    <template x-if="isPasoActivo(paso.id)">
                                        <span class="absolute inset-0 rounded-full animate-ping"
                                              style="background: var(--color-primary); opacity: 0.25"></span>
                                    </template>
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center relative z-10"
                                         :style="isPasoCompleto(paso.id)
                                             ? 'background:#059669'
                                             : isPasoActivo(paso.id)
                                                 ? 'background:var(--color-primary)'
                                                 : 'background:var(--color-border)'">
                                        <template x-if="isPasoCompleto(paso.id)">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="white" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </template>
                                        <template x-if="isPasoActivo(paso.id)">
                                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-30" cx="12" cy="12" r="10" stroke="white" stroke-width="4"/>
                                                <path class="opacity-90" fill="white" d="M4 12a8 8 0 018-8v8H4z"/>
                                            </svg>
                                        </template>
                                        <template x-if="!isPasoCompleto(paso.id) && !isPasoActivo(paso.id)">
                                            <span class="w-2 h-2 rounded-full bg-white opacity-40"></span>
                                        </template>
                                    </div>
                                </div>

                                {{-- Conector vertical --}}
                                <template x-if="i < pasosVisibles.length - 1">
                                    <div class="w-0.5 my-1"
                                         style="flex: 1; min-height: 32px"
                                         :style="isPasoCompleto(paso.id) ? 'background:#059669' : 'background:var(--color-border)'">
                                    </div>
                                </template>

                            </div>

                            {{-- Texto del paso --}}
                            <div :class="i < pasosVisibles.length - 1 ? 'pb-8 pt-1' : 'pt-1'">
                                <p class="text-xs leading-tight"
                                   :style="isPasoCompleto(paso.id)
                                       ? 'color:#059669; font-weight:400'
                                       : isPasoActivo(paso.id)
                                           ? 'color:var(--color-text); font-weight:600'
                                           : 'color:var(--color-muted); font-weight:400'">
                                    <span x-text="paso.label"></span>
                                    <template x-if="paso.opcional && pasosVistos.includes(paso.id)">
                                        <span class="ml-1 text-[9px] font-medium px-1 py-0.5 rounded"
                                              style="background:var(--color-sidebar-active); color:var(--color-primary)">
                                            GÉNESIS
                                        </span>
                                    </template>
                                </p>
                                <template x-if="isPasoActivo(paso.id)">
                                    <p class="text-[11px] mt-1 leading-relaxed" style="color: var(--color-muted)"
                                       x-text="paso.desc"></p>
                                </template>
                            </div>

                        </div>
                    </template>
                </div>

            </div>

            {{-- ── RESULTADO: acciones + badge + resoluciones citadas ──────────── --}}
            <div x-show="estado === 'resultado'" class="space-y-3 overflow-y-auto min-h-0 pr-1">

                <button @click="nuevaConsulta()"
                        class="w-full flex items-center justify-center gap-2 h-9 rounded-lg text-xs font-medium hover:opacity-90"
                        style="background-color: var(--color-primary); color: white;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                    Nueva consulta
                </button>

                {{-- Confianza --}}
                @if($confianza)
                    <div class="bg-white rounded-lg p-3" style="border: 1px solid var(--color-border)">
                        <p class="text-[10px] font-semibold uppercase tracking-wide mb-2" style="color: var(--color-subtle)">
                            Confianza
                        </p>
                        @php
                            $badgeColors = [
                                'alta'  => ['bg' => '#f0fdf4', 'border' => '#bbf7d0', 'color' => '#166534'],
                                'media' => ['bg' => '#fffbeb', 'border' => '#fde68a', 'color' => '#92400e'],
                                'baja'  => ['bg' => '#fff1f2', 'border' => '#fecdd3', 'color' => '#9f1239'],
                            ];
                            $bc = $badgeColors[$confianza] ?? $badgeColors['baja'];
                        @endphp
                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full"
                              style="background:{{ $bc['bg'] }}; border:1px solid {{ $bc['border'] }}; color:{{ $bc['color'] }}">
                            {{ ucfirst($confianza) }}
                        </span>
                    </div>
                @endif

                {{-- Resoluciones citadas --}}
                @if(!empty($resolucionesCitadas))
                    <div class="bg-white rounded-lg p-3" style="border: 1px solid var(--color-border)">
                        <p class="text-[10px] font-semibold uppercase tracking-wide mb-2" style="color: var(--color-subtle)">
                            Resoluciones citadas
                        </p>
                        <div class="space-y-1">
                            @foreach($resolucionesCitadas as $nro)
                                <p class="text-[11px] font-medium" style="color: var(--color-text)">{{ $nro }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>

            {{-- ── ERROR: mensaje + reintentar ─────────────────────────────────── --}}
            <div x-show="estado === 'error'" class="space-y-3">

                <div class="rounded-lg p-3" style="background:#fff1f2; border:1px solid #fecdd3">
                    <p class="text-[10px] font-semibold uppercase tracking-wide mb-1" style="color:#9f1239">
                        Consulta no procesada
                    </p>
                    @if($errorMsg)
                        <p class="text-[11px] leading-relaxed" style="color:#9f1239">{{ $errorMsg }}</p>
                    @endif
                </div>

                <button @click="nuevaConsulta()"
                        class="w-full flex items-center justify-center h-9 rounded-lg text-xs font-medium hover:opacity-80"
                        style="border: 1px solid var(--color-border); color: var(--color-muted);">
                    Intentar de nuevo
                </button>

            </div>

        </div>

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- PANEL DERECHO                                                      --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        <div class="flex-1 min-w-0 min-h-0 flex flex-col gap-3">

            {{-- ── IDLE: formulario de búsqueda ──────────────────────────────── --}}
            <template x-if="estado === 'idle'">
                <div class="flex-1 min-h-0 flex flex-col gap-3">

                    {{-- Textarea --}}
                    <div class="flex-1 min-h-0 bg-white rounded-xl flex flex-col"
                         style="border: 1px solid var(--color-border)">
                        <div class="px-5 pt-4 pb-2 flex-shrink-0"
                             style="border-bottom: 1px solid var(--color-border)">
                            <p class="text-xs font-medium" style="color: var(--color-text)">Tu consulta</p>
                        </div>
                        <textarea
                            x-model="preguntaInput"
                            @keydown.ctrl.enter.prevent="buscar()"
                            placeholder="Ej: ¿Cuáles son los criterios para la inamovilidad laboral de una trabajadora embarazada? ¿Qué dice el TSJ Bolivia sobre el despido durante el estado de gestación?"
                            class="flex-1 w-full px-5 py-4 resize-none focus:outline-none text-xs leading-relaxed"
                            style="color: var(--color-text); background: transparent"
                            maxlength="500"></textarea>
                    </div>

                    {{-- Barra inferior --}}
                    <div class="flex-shrink-0 bg-white rounded-xl px-4 py-3 flex items-center gap-4"
                         style="border: 1px solid var(--color-border)">

                        <span class="text-[11px] flex-shrink-0" style="color: var(--color-muted)">
                            <span x-text="preguntaInput.length"></span>/500
                            <template x-if="validationError">
                                <span style="color: #dc2626"> · <span x-text="validationError"></span></span>
                            </template>
                        </span>

                        <span class="text-[11px] flex-shrink-0" style="color: var(--color-subtle)">Ctrl + Enter</span>

                        <div class="flex-1"></div>

                        <button @click="buscar()"
                                :disabled="preguntaInput.trim().length < 10"
                                class="flex items-center gap-2 h-9 px-5 rounded-lg text-xs font-medium hover:opacity-90 disabled:opacity-50 flex-shrink-0"
                                style="background-color: var(--color-primary); color: white;">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                            </svg>
                            Buscar jurisprudencia
                        </button>

                    </div>

                </div>
            </template>

            {{-- ── BUSCANDO: eco de la consulta enviada ──────────────────────── --}}
            <template x-if="estado === 'buscando'">
                <div class="flex-1 min-h-0 bg-white rounded-xl flex flex-col"
                     style="border: 1px solid var(--color-border)">
                    <div class="px-6 py-3 flex-shrink-0"
                         style="border-bottom: 1px solid var(--color-border)">
                        <p class="text-xs font-medium" style="color: var(--color-text-secondary)">Consulta enviada</p>
                    </div>
                    <div class="flex-1 overflow-y-auto px-6 py-5 min-h-0">
                        <p class="text-xs leading-relaxed whitespace-pre-wrap"
                           style="color: var(--color-text)"
                           x-text="preguntaInput"></p>
                    </div>
                </div>
            </template>

            {{-- ── RESULTADO: respuesta markdown + fuentes ──────────────────── --}}
            @if($respuesta || !empty($fuentes))
                <div class="flex-1 min-h-0 flex flex-col gap-3 overflow-y-auto min-h-0"
                     x-show="estado === 'resultado'">

                    {{-- Respuesta markdown --}}
                    <div class="bg-white rounded-xl flex-shrink-0" style="border: 1px solid var(--color-border)">
                        <div class="px-6 py-3 flex-shrink-0"
                             style="border-bottom: 1px solid var(--color-border)">
                            <p class="text-xs font-semibold" style="color: var(--color-text)">Análisis jurídico</p>
                        </div>
                        <div class="px-6 py-5">
                            <div
                                x-data="{ mdHtml: '' }"
                                x-effect="mdHtml = (window.marked && $wire.respuesta) ? window.marked.parse($wire.respuesta) : $wire.respuesta"
                                class="text-xs leading-relaxed juris-md"
                                x-html="mdHtml"
                                style="color: var(--color-text)">
                            </div>
                        </div>
                    </div>

                    {{-- Fuentes / resoluciones --}}
                    @if(!empty($fuentes))
                        <div class="bg-white rounded-xl flex-shrink-0" style="border: 1px solid var(--color-border)">
                            <div class="px-6 py-3" style="border-bottom: 1px solid var(--color-border)">
                                <p class="text-xs font-semibold" style="color: var(--color-text)">
                                    Resoluciones consultadas
                                    <span class="ml-1.5 text-[10px] font-normal" style="color: var(--color-muted)">
                                        {{ count($fuentes) }} encontradas
                                    </span>
                                </p>
                            </div>
                            <div class="px-6 py-4 space-y-2">
                                @foreach($fuentes as $fuente)
                                    <div class="flex items-center justify-between gap-4 px-3 py-2.5 rounded-lg"
                                         style="background: var(--color-page-bg); border: 1px solid var(--color-border)">
                                        <div class="min-w-0">
                                            <p class="text-[12px] font-semibold truncate" style="color: var(--color-text)">
                                                {{ $fuente['nro'] ?? '-' }}
                                            </p>
                                            <p class="text-[11px] mt-0.5" style="color: var(--color-muted)">
                                                {{ $fuente['fecha'] ?? 'Sin fecha' }}
                                                @if(!empty($fuente['sala']) && $fuente['sala'] !== '-')
                                                    · {{ $fuente['sala'] }}
                                                @endif
                                                @if(!empty($fuente['materia']) && $fuente['materia'] !== '-')
                                                    · {{ $fuente['materia'] }}
                                                @endif
                                            </p>
                                        </div>
                                        <span class="flex-shrink-0 text-[11px] font-semibold px-2 py-0.5 rounded-full whitespace-nowrap"
                                              style="background:var(--color-sidebar-active); color:var(--color-primary); border:1px solid var(--color-primary); opacity:0.7">
                                            {{ round(($fuente['score'] ?? 0) * 100) }}%
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
            @endif

            {{-- ── ERROR: panel derecho ──────────────────────────────────────── --}}
            <template x-if="estado === 'error'">
                <div class="flex-1 min-h-0 bg-white rounded-xl flex items-center justify-center"
                     style="border: 1px solid var(--color-border)">
                    <div class="text-center px-8">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto mb-4"
                             style="background:#fff1f2">
                            <svg class="w-6 h-6" fill="none" stroke="#9f1239" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v2m0 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium mb-1" style="color: var(--color-text)">No se pudo completar la consulta</p>
                        <p class="text-xs" style="color: var(--color-muted)">Revisá el panel izquierdo para más detalles</p>
                    </div>
                </div>
            </template>

        </div>
    </div>

</div>

<style>
.juris-md h1, .juris-md h2 { font-size: 0.85rem; font-weight: 700; margin: 1rem 0 0.4rem; color: var(--color-text); }
.juris-md h3 { font-size: 0.8rem; font-weight: 600; margin: 0.8rem 0 0.3rem; }
.juris-md p  { margin-bottom: 0.6rem; line-height: 1.75; }
.juris-md p:last-child { margin-bottom: 0; }
.juris-md strong { font-weight: 600; color: var(--color-text); }
.juris-md ul, .juris-md ol { margin: 0.4rem 0 0.6rem 1.25rem; }
.juris-md li { margin-bottom: 0.25rem; }
.juris-md blockquote { border-left: 3px solid var(--color-primary); padding-left: 0.75rem; margin: 0.6rem 0; opacity: 0.8; }
</style>

<script>
if (!window._markedLoaded) {
    window._markedLoaded = true;
    var s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/marked/marked.min.js';
    document.head.appendChild(s);
}
</script>
