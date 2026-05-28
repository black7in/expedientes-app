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
            {{ $generacion->created_at->format('d/m/Y H:i') }}
        </span>
    </div>

    @if($generacion->estado === 'error')
        <div class="max-w-lg mx-auto mt-12 bg-white rounded-xl p-6 text-center" style="border: 1px solid var(--color-border)">
            <p class="text-sm font-medium mb-2" style="color: #dc2626">Error en la generación</p>
            <p class="text-xs mb-5" style="color: var(--color-muted)">{{ $generacion->error_msg }}</p>
            <a href="{{ route('generacion.crear') }}" wire:navigate
               class="inline-flex items-center gap-1.5 h-9 px-4 text-xs font-medium text-white rounded-lg"
               style="background-color: var(--color-primary)">Nueva generación</a>
        </div>

    @elseif(! $generacion->documento_html)
        <div class="max-w-sm mx-auto mt-16 text-center">
            <p class="text-sm font-medium mb-2" style="color: var(--color-text)">Documento en proceso</p>
            <p class="text-xs mb-5" style="color: var(--color-muted)">Estado: {{ $generacion->estado }}</p>
            <a href="{{ route('generacion.crear') }}" wire:navigate
               class="text-xs" style="color: var(--color-primary)">← Volver</a>
        </div>

    @else

        <div class="flex gap-5" style="height: calc(100vh - 140px); overflow: hidden">

            {{-- ── Panel izquierdo ─────────────────────────────────────────── --}}
            <div class="w-60 flex-shrink-0 space-y-3 overflow-y-auto min-h-0 pr-1">

                {{-- Acciones --}}
                <button wire:click="guardar"
                        wire:loading.attr="disabled"
                        wire:target="guardar"
                        class="w-full flex items-center justify-center gap-2 h-9 rounded-lg text-xs font-medium hover:opacity-90 disabled:opacity-60"
                        style="background-color: var(--color-primary); color: white;">
                    <span wire:loading.remove wire:target="guardar">
                        @if($guardado) Guardado ✓ @else Guardar cambios @endif
                    </span>
                    <span wire:loading wire:target="guardar">Guardando...</span>
                </button>

                <a href="{{ route('generacion.descargar', $generacion_id) }}" target="_blank"
                   class="w-full flex items-center justify-center gap-2 h-9 rounded-lg text-xs font-medium hover:opacity-80"
                   style="border: 1px solid var(--color-border); color: var(--color-muted);">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Exportar a Word
                </a>

                <a href="{{ route('generacion.crear') }}" wire:navigate
                   class="w-full flex items-center justify-center h-8 rounded-lg text-xs hover:opacity-80"
                   style="color: var(--color-muted)">+ Nueva generación</a>

                {{-- Fuentes --}}
                @php
                    $leyes           = $generacion->fuentes['leyes'] ?? [];
                    $leyesProcesales = $generacion->fuentes['leyes_procesales'] ?? [];
                    $moldeFormato    = $generacion->fuentes['molde_formato'] ?? null;
                    $todasLeyes      = array_merge($leyes, $leyesProcesales);

                    // Agrupar por nombre de ley
                    $grupos = [];
                    foreach ($todasLeyes as $l) {
                        $key = $l['ley'] ?? 'Ley';
                        $grupos[$key][] = $l;
                    }
                @endphp

                @if(!empty($grupos) || $moldeFormato)
                    <div class="bg-white rounded-lg p-3" style="border: 1px solid var(--color-border)">
                        <p class="text-[10px] font-semibold uppercase mb-3" style="color: var(--color-subtle)">
                            Fuentes utilizadas
                        </p>

                        @foreach($grupos as $nombreLey => $articulos)
                            <div class="mb-3">
                                <p class="text-[10px] font-semibold mb-1.5" style="color: var(--color-text-secondary)">
                                    {{ $nombreLey }}
                                </p>
                                @foreach($articulos as $art)
                                    <div class="flex items-baseline gap-1.5 mb-1">
                                        <span class="text-[10px] font-mono font-semibold flex-shrink-0"
                                              style="color: var(--color-primary)">
                                            Art. {{ $art['articulo'] }}
                                        </span>
                                        @if(!empty($art['titulo']))
                                            <span class="text-[11px] leading-tight" style="color: var(--color-muted)">
                                                {{ $art['titulo'] }}
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        @if($moldeFormato)
                            <div @if(!empty($grupos)) style="border-top: 1px solid var(--color-border); margin-top: 0.5rem; padding-top: 0.5rem" @endif>
                                <span class="text-[10px]" style="color: var(--color-muted)">
                                    Formato: <span style="color: var(--color-text-secondary)">{{ $moldeFormato }}</span>
                                </span>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="bg-white rounded-lg p-3" style="border: 1px solid var(--color-border)">
                        <p class="text-[10px] font-semibold uppercase mb-1" style="color: var(--color-subtle)">Fuentes</p>
                        <p class="text-[11px]" style="color: var(--color-muted)">
                            {{ $generacion->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                @endif

                {{-- Revisiones --}}
                @php
                    $revisiones = array_merge(
                        array_map(fn($a) => str_replace('_', ' ', $a), $generacion->advertencias ?? []),
                        array_map(fn($v) => $v['detalle'] ?? '', $generacion->validaciones ?? [])
                    );
                    $revisiones = array_filter($revisiones);
                @endphp
                @if(!empty($revisiones))
                    <div class="rounded-lg p-3" style="background:#fffbeb; border:1px solid #fde68a">
                        <p class="text-[10px] font-semibold uppercase mb-2" style="color:#92400e">Revisiones</p>
                        @foreach($revisiones as $r)
                            <p class="text-[11px] leading-relaxed" style="color:#92400e">· {{ $r }}</p>
                        @endforeach
                    </div>
                @endif

            </div>

            {{-- ── Panel derecho — TipTap ──────────────────────────────────── --}}
            <div class="flex-1 min-w-0 min-h-0 overflow-hidden">
                <div class="bg-white rounded-xl h-full flex flex-col" style="border: 1px solid var(--color-border)"
                     wire:ignore
                     x-data="tiptapEditor(@js($contenidoEditado), 'contenidoEditado')"
                     x-destroy="destroy()">

                    {{-- Toolbar --}}
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

                    {{-- Área del editor --}}
                    <div x-ref="content" class="flex-1 overflow-y-auto px-8 py-6 min-h-0"></div>

                </div>
            </div>

        </div>

    @endif

</div>
