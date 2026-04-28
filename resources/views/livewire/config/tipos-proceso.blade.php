<div>
    <div class="flex items-center justify-between mb-5">
        <div>
            <h2 class="text-sm font-semibold" style="color: var(--color-text)">Tipos de proceso</h2>
            <p class="text-xs mt-0.5" style="color: var(--color-muted)">Configuración de tipos de proceso según Ley 439</p>
        </div>
    </div>

    <div class="space-y-3">
        @foreach($tiposProceso as $tipo)
            <div class="bg-white rounded-lg overflow-hidden" style="border: 1px solid var(--color-border)">
                {{-- Header row --}}
                <div class="flex items-center gap-3 px-4 py-3">
                    {{-- Expand toggle --}}
                    <button type="button" wire:click="toggleExpandido('{{ $tipo->id }}')"
                            class="flex-shrink-0 w-5 h-5 flex items-center justify-center rounded transition-colors hover:bg-slate-100"
                            style="color: var(--color-muted)">
                        <svg class="w-3.5 h-3.5 transition-transform {{ $expandido === $tipo->id ? 'rotate-90' : '' }}"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>

                    {{-- Name + area --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold" style="color: var(--color-text)">{{ $tipo->nombre }}</p>
                        <p class="text-[11px] mt-0.5 capitalize" style="color: var(--color-muted)">{{ $tipo->area_derecho }}</p>
                    </div>

                    {{-- Etapas count --}}
                    <span class="text-[11px] tabular-nums flex-shrink-0" style="color: var(--color-subtle)">
                        {{ $tipo->etapas->count() }} etapas
                    </span>

                    {{-- Active toggle --}}
                    <button
                        wire:click="toggleActivo('{{ $tipo->id }}')"
                        type="button"
                        title="{{ $tipo->activo ? 'Desactivar' : 'Activar' }}"
                        class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1"
                        style="{{ $tipo->activo ? 'background-color: var(--color-primary)' : 'background-color: #d1d5db;' }}"
                    >
                        <span class="pointer-events-none inline-block h-4 w-4 rounded-full bg-white shadow transform transition duration-200 {{ $tipo->activo ? 'translate-x-4' : 'translate-x-0' }}"></span>
                    </button>
                </div>

                {{-- Etapas expandidas --}}
                @if($expandido === $tipo->id)
                    <div style="border-top: 1px solid var(--color-border)">
                        <table class="w-full text-xs">
                            <thead>
                                <tr style="background-color: #fafafa; border-bottom: 1px solid var(--color-border)">
                                    <th class="text-left px-4 py-2 font-semibold uppercase tracking-wide text-[10px]" style="color: var(--color-muted)">#</th>
                                    <th class="text-left px-4 py-2 font-semibold uppercase tracking-wide text-[10px]" style="color: var(--color-muted)">Etapa</th>
                                    <th class="text-left px-4 py-2 font-semibold uppercase tracking-wide text-[10px]" style="color: var(--color-muted)">Tipo</th>
                                    <th class="text-left px-4 py-2 font-semibold uppercase tracking-wide text-[10px]" style="color: var(--color-muted)">Plazo</th>
                                    <th class="text-left px-4 py-2 font-semibold uppercase tracking-wide text-[10px]" style="color: var(--color-muted)">Cómputo</th>
                                    <th class="px-4 py-2 font-semibold uppercase tracking-wide text-[10px]" style="color: var(--color-muted)">Crítica</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y" style="border-color: var(--color-border)">
                                @foreach($tipo->etapas as $etapa)
                                    <tr class="{{ $etapa->es_critica ? 'bg-amber-50/40' : '' }}">
                                        <td class="px-4 py-2.5 tabular-nums font-mono text-[11px]" style="color: var(--color-subtle)">
                                            {{ $etapa->orden }}
                                        </td>
                                        <td class="px-4 py-2.5">
                                            <div class="flex items-center gap-1.5">
                                                <span style="color: var(--color-text)">{{ $etapa->nombre }}</span>
                                                @if($etapa->es_critica)
                                                    <span class="inline-flex items-center rounded-full px-1.5 py-0.5 text-[9px] font-semibold bg-amber-100 text-amber-700 ring-1 ring-inset ring-amber-600/20">
                                                        CRÍTICA
                                                    </span>
                                                @endif
                                            </div>
                                            @if($etapa->descripcion)
                                                <p class="text-[11px] mt-0.5" style="color: var(--color-muted)">{{ $etapa->descripcion }}</p>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2.5 capitalize" style="color: var(--color-text-secondary)">{{ $etapa->tipo }}</td>
                                        <td class="px-4 py-2.5 tabular-nums" style="color: var(--color-text-secondary)">
                                            @if($etapa->plazo_dias)
                                                {{ $etapa->plazo_dias }} días
                                            @else
                                                <span style="color: var(--color-subtle)">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2.5 capitalize" style="color: var(--color-text-secondary)">
                                            {{ $etapa->tipo_computo ?? '—' }}
                                        </td>
                                        <td class="px-4 py-2.5 text-center">
                                            @if($etapa->es_critica)
                                                <svg class="w-3.5 h-3.5 mx-auto text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Loading overlay --}}
    <div wire:loading.delay class="fixed inset-0 bg-white/30 backdrop-blur-[1px] z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg px-4 py-2.5 flex items-center gap-2 shadow-sm text-xs" style="border: 1px solid var(--color-border); color: var(--color-muted)">
            <svg class="animate-spin w-3.5 h-3.5" style="color: var(--color-subtle)" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            Cargando...
        </div>
    </div>
</div>
