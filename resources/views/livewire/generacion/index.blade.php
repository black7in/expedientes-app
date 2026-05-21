<div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-lg font-semibold" style="color: var(--color-text)">Historial de generaciones</h1>
            <p class="text-xs mt-0.5" style="color: var(--color-muted)">Documentos generados con IA</p>
        </div>
        <a href="{{ route('generacion.crear') }}" wire:navigate
           class="inline-flex items-center gap-1.5 h-8 px-3 text-xs font-medium text-white rounded-md hover:opacity-90"
           style="background-color: var(--color-primary)">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Nueva generación
        </a>
    </div>

    {{-- Error --}}
    @if($error)
        <div class="flex items-start gap-3 px-4 py-3 rounded-lg mb-4 text-xs"
             style="background-color: #fef2f2; border: 1px solid #fecaca; color: #b91c1c">
            {{ $error }}
        </div>
    @endif

    <div class="bg-white rounded-lg" style="border: 1px solid var(--color-border)">

        @forelse($generaciones as $gen)
            <div class="flex items-center justify-between px-5 py-4"
                 @if(!$loop->first) style="border-top: 1px solid var(--color-border)" @endif>

                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-8 h-8 rounded-md flex items-center justify-center flex-shrink-0"
                         style="background-color: var(--color-sidebar-active)">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                             style="color: var(--color-primary)">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <p class="text-xs font-medium capitalize" style="color: var(--color-text)">
                                {{ str_replace('_', ' ', $gen['tipo_documento'] ?? '') }}
                            </p>
                            @if(!empty($gen['subtipo']))
                                <span class="text-[10px] px-1.5 py-0.5 rounded"
                                      style="background-color: var(--color-sidebar-active); color: var(--color-primary)">
                                    {{ $gen['subtipo'] }}
                                </span>
                            @endif
                            {{-- Estado --}}
                            @php
                                $estado = $gen['estado'] ?? '';
                                $estadoColor = match($estado) {
                                    'completado' => '#059669',
                                    'error'      => '#dc2626',
                                    'generando'  => '#d97706',
                                    'editado'    => '#7c3aed',
                                    default      => 'var(--color-muted)',
                                };
                            @endphp
                            <span class="text-[10px]" style="color: {{ $estadoColor }}">{{ $estado }}</span>
                        </div>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-[11px]" style="color: var(--color-muted)">
                                {{ \Carbon\Carbon::parse($gen['created_at'])->diffForHumans() }}
                            </span>
                            @if(!empty($gen['tokens_total']))
                                <span style="color: var(--color-border-strong)">·</span>
                                <span class="text-[11px]" style="color: var(--color-muted)">
                                    {{ number_format($gen['tokens_total']) }} tokens
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    @if(($gen['estado'] ?? '') === 'completado' || ($gen['estado'] ?? '') === 'editado')
                        <a href="{{ route('generacion.descargar', $gen['id']) }}" target="_blank"
                           class="text-[11px] hover:underline" style="color: var(--color-muted)">
                            .docx
                        </a>
                    @endif
                    <a href="{{ route('generacion.show', $gen['id']) }}" wire:navigate
                       class="text-[11px] hover:underline" style="color: var(--color-primary)">
                        Ver →
                    </a>
                </div>

            </div>
        @empty
            <div class="py-16 text-center">
                <svg class="w-10 h-10 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                     style="color: var(--color-border-strong)">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
                <p class="text-sm font-medium" style="color: var(--color-text-secondary)">Sin generaciones aún</p>
                <p class="text-xs mt-1 mb-4" style="color: var(--color-muted)">
                    Genera tu primer borrador jurídico con IA
                </p>
                <a href="{{ route('generacion.crear') }}" wire:navigate
                   class="inline-flex items-center gap-1.5 h-8 px-4 text-xs font-medium text-white rounded-md hover:opacity-90"
                   style="background-color: var(--color-primary)">
                    Generar documento
                </a>
            </div>
        @endforelse
    </div>

</div>
