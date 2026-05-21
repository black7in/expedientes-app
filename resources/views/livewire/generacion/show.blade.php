<div>

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-1.5 text-xs mb-5" style="color: var(--color-muted)">
        <a href="{{ route('dashboard') }}" wire:navigate class="hover:underline" style="color: var(--color-muted)">Inicio</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <a href="{{ route('generacion.index') }}" wire:navigate class="hover:underline" style="color: var(--color-muted)">Generaciones</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span style="color: var(--color-text-secondary)">{{ $generacion['tipo_documento'] ?? '' }} / {{ $generacion['subtipo'] ?? '' }}</span>
    </div>

    {{-- Error global --}}
    @if($error)
        <div class="flex items-start gap-3 px-4 py-3 rounded-lg mb-4 text-xs"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: #b91c1c">
            {{ $error }}
        </div>
    @endif

    @if(empty($generacion))
        <div class="flex items-center justify-center py-24">
            <p class="text-sm" style="color: var(--color-muted)">Generación no encontrada.</p>
        </div>
    @else

    <div class="flex gap-5 items-start">

        {{-- ── PANEL IZQUIERDO — Info + Secciones ─────────────────────────────── --}}
        <div class="w-72 flex-shrink-0 space-y-4">

            {{-- Info del documento --}}
            <div class="bg-white rounded-lg p-4" style="border: 1px solid var(--color-border)">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[10px] font-semibold uppercase tracking-wide" style="color: var(--color-subtle)">Documento</p>
                    @php
                        $estado = $generacion['estado'] ?? '';
                        $estadoColor = match($estado) {
                            'completado' => '#059669',
                            'editado'    => '#7c3aed',
                            'error'      => '#dc2626',
                            'generando'  => '#d97706',
                            default      => 'var(--color-muted)',
                        };
                    @endphp
                    <span class="text-[10px] font-medium px-1.5 py-0.5 rounded"
                          style="color: {{ $estadoColor }}; background: {{ $estadoColor }}18">
                        {{ $estado }}
                    </span>
                </div>
                <p class="text-xs font-semibold capitalize" style="color: var(--color-text)">
                    {{ str_replace('_', ' ', $generacion['tipo_documento'] ?? '') }}
                    @if(!empty($generacion['subtipo']))
                        / {{ $generacion['subtipo'] }}
                    @endif
                </p>
                <p class="text-[11px] mt-1" style="color: var(--color-muted)">
                    {{ str_replace('_', ' ', $generacion['formato_salida'] ?? '') }}
                </p>
                @if(!empty($generacion['tokens_input']) || !empty($generacion['tokens_output']))
                    <p class="text-[11px] mt-1" style="color: var(--color-muted)">
                        {{ number_format(($generacion['tokens_input'] ?? 0) + ($generacion['tokens_output'] ?? 0)) }} tokens totales
                    </p>
                @endif
                @if(!empty($generacion['created_at']))
                    <p class="text-[11px] mt-1" style="color: var(--color-muted)">
                        {{ \Carbon\Carbon::parse($generacion['created_at'])->format('d/m/Y H:i') }}
                    </p>
                @endif
            </div>

            {{-- Acciones --}}
            <div class="flex flex-col gap-2">
                <a href="{{ route('generacion.descargar', $generacion_id) }}" target="_blank"
                   class="w-full flex items-center justify-center gap-2 py-2 rounded-lg text-xs font-medium transition-opacity hover:opacity-80"
                   style="background-color: var(--color-primary); color: white;">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Descargar .docx
                </a>
                @if(!$editando)
                    <button wire:click="iniciarEdicion"
                            class="w-full flex items-center justify-center gap-2 py-2 rounded-lg text-xs font-medium transition-opacity hover:opacity-80"
                            style="border: 1px solid var(--color-border); color: var(--color-muted);">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar documento
                    </button>
                @endif
            </div>

            {{-- Secciones --}}
            @if(!empty($secciones))
                <div class="bg-white rounded-lg" style="border: 1px solid var(--color-border)">
                    <p class="text-[10px] font-semibold uppercase tracking-wide px-4 pt-3 pb-2" style="color: var(--color-subtle)">
                        Secciones ({{ count($secciones) }})
                    </p>
                    @foreach($secciones as $sec)
                        <div @if(!$loop->first) style="border-top: 1px solid var(--color-border)" @endif>
                            <div class="px-4 py-2.5">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="min-w-0">
                                        <p class="text-[11px] font-medium truncate" style="color: var(--color-text)">
                                            {{ $sec['seccion_id'] }}
                                        </p>
                                        @if(!empty($sec['tokens_output']))
                                            <p class="text-[10px]" style="color: var(--color-muted)">
                                                {{ $sec['tokens_output'] }} tokens out
                                                @if(($sec['regenerada_count'] ?? 0) > 0)
                                                    · {{ $sec['regenerada_count'] }}x regen
                                                @endif
                                            </p>
                                        @endif
                                    </div>
                                    @if($sec['seccion_id'] !== $seccion_regenerando)
                                        <button wire:click="abrirRegeneracion('{{ $sec['seccion_id'] }}')"
                                                class="flex-shrink-0 text-[10px] px-2 py-1 rounded transition-colors hover:opacity-80"
                                                style="border: 1px solid var(--color-border); color: var(--color-muted);">
                                            Regen.
                                        </button>
                                    @endif
                                </div>

                                {{-- Panel de regeneración inline --}}
                                @if($sec['seccion_id'] === $seccion_regenerando)
                                    <div class="mt-2 space-y-2">
                                        <textarea wire:model="feedback" rows="2"
                                                  placeholder="Feedback opcional (ej: hacelo más formal)..."
                                                  class="w-full text-[11px] rounded px-2 py-1.5 resize-none focus:outline-none"
                                                  style="border: 1px solid var(--color-border); color: var(--color-text); background: white;"></textarea>
                                        <div class="flex gap-1.5">
                                            <button wire:click="regenerar"
                                                    wire:loading.attr="disabled"
                                                    wire:target="regenerar"
                                                    class="flex-1 text-[10px] py-1.5 rounded font-medium disabled:opacity-60"
                                                    style="background-color: var(--color-primary); color: white;">
                                                <span wire:loading.remove wire:target="regenerar">Regenerar</span>
                                                <span wire:loading wire:target="regenerar">...</span>
                                            </button>
                                            <button wire:click="cancelarRegeneracion"
                                                    class="flex-1 text-[10px] py-1.5 rounded"
                                                    style="border: 1px solid var(--color-border); color: var(--color-muted);">
                                                Cancelar
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>

        {{-- ── PANEL DERECHO — Contenido ───────────────────────────────────────── --}}
        <div class="flex-1 min-w-0">

            @if($editando)
                {{-- Modo edición --}}
                <div class="bg-white rounded-lg" style="border: 1px solid var(--color-border)">
                    <div class="flex items-center justify-between px-5 py-3"
                         style="border-bottom: 1px solid var(--color-border)">
                        <p class="text-xs font-medium" style="color: var(--color-text)">Editando documento completo</p>
                        <div class="flex items-center gap-2">
                            <button wire:click="guardarEdicion"
                                    wire:loading.attr="disabled"
                                    wire:target="guardarEdicion"
                                    class="text-xs px-3 py-1.5 rounded-md font-medium disabled:opacity-60"
                                    style="background-color: var(--color-primary); color: white;">
                                <span wire:loading.remove wire:target="guardarEdicion">Guardar</span>
                                <span wire:loading wire:target="guardarEdicion">Guardando...</span>
                            </button>
                            <button wire:click="cancelarEdicion"
                                    class="text-xs px-3 py-1.5 rounded-md"
                                    style="border: 1px solid var(--color-border); color: var(--color-muted);">
                                Cancelar
                            </button>
                        </div>
                    </div>
                    <div class="p-5">
                        <textarea wire:model="contenido_edit"
                                  class="w-full text-xs leading-relaxed font-mono focus:outline-none resize-none"
                                  style="color: var(--color-text); min-height: 70vh;"
                                  spellcheck="false"></textarea>
                    </div>
                </div>
            @else
                {{-- Modo lectura --}}
                <div class="bg-white rounded-lg" style="border: 1px solid var(--color-border)">
                    <div class="flex items-center justify-between px-5 py-3"
                         style="border-bottom: 1px solid var(--color-border)">
                        <p class="text-xs font-medium" style="color: var(--color-text-secondary)">Contenido del documento</p>
                        @if($regenerando)
                            <span class="flex items-center gap-1.5 text-xs" style="color: var(--color-primary)">
                                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                </svg>
                                Regenerando sección...
                            </span>
                        @endif
                    </div>
                    <div class="px-6 py-5">
                        @if(!empty($generacion['contenido_actual']))
                            <pre class="text-xs leading-relaxed whitespace-pre-wrap font-sans"
                                 style="color: var(--color-text)">{{ $generacion['contenido_actual'] }}</pre>
                        @else
                            <p class="text-xs py-12 text-center" style="color: var(--color-muted)">
                                Sin contenido disponible.
                            </p>
                        @endif
                    </div>
                </div>
            @endif

        </div>

    </div>
    @endif

</div>
