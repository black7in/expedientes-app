<div class="space-y-6">

    {{-- Bienvenida --}}
    <div>
        <h2 class="text-lg font-semibold" style="color: var(--color-text)">
            Buen día, {{ explode(' ', auth()->user()->nombre)[0] }}
        </h2>
        <p class="text-xs mt-0.5" style="color: var(--color-muted)">
            Aquí está el resumen de tu actividad
        </p>
    </div>

    {{-- Métricas --}}
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">

        <div class="bg-white rounded-lg p-5 space-y-3" style="border: 1px solid var(--color-border)">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wide" style="color: var(--color-muted)">Total</span>
                <div class="w-7 h-7 rounded-md flex items-center justify-center"
                     style="background-color: var(--color-primary-light)">
                    <svg class="w-3.5 h-3.5" style="color: var(--color-primary-text)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-bold tabular-nums" style="color: var(--color-text)">{{ $totalExpedientes }}</p>
                <p class="text-[11px] mt-0.5" style="color: var(--color-subtle)">expedientes</p>
            </div>
        </div>

        <div class="bg-white rounded-lg p-5 space-y-3" style="border: 1px solid var(--color-border)">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wide" style="color: var(--color-muted)">Activos</span>
                <div class="w-7 h-7 rounded-md flex items-center justify-center bg-emerald-50">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-bold tabular-nums" style="color: var(--color-text)">{{ $expedientesActivos }}</p>
                <p class="text-[11px] mt-0.5" style="color: var(--color-subtle)">en curso</p>
            </div>
        </div>

        <div class="bg-white rounded-lg p-5 space-y-3" style="border: 1px solid var(--color-border)">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wide" style="color: var(--color-muted)">Pendientes</span>
                <div class="w-7 h-7 rounded-md flex items-center justify-center bg-amber-50">
                    <svg class="w-3.5 h-3.5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-bold tabular-nums text-amber-600">{{ $documentosPendientes }}</p>
                <p class="text-[11px] mt-0.5" style="color: var(--color-subtle)">docs. por procesar</p>
            </div>
        </div>

        <div class="bg-white rounded-lg p-5 space-y-3" style="border: 1px solid var(--color-border)">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-semibold uppercase tracking-wide" style="color: var(--color-muted)">Este mes</span>
                <div class="w-7 h-7 rounded-md flex items-center justify-center"
                     style="background-color: var(--color-primary-light)">
                    <svg class="w-3.5 h-3.5" style="color: var(--color-primary-text)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <div>
                <p class="text-2xl font-bold tabular-nums" style="color: var(--color-text)">{{ $expedientesMes }}</p>
                <p class="text-[11px] mt-0.5" style="color: var(--color-subtle)">nuevos expedientes</p>
            </div>
        </div>

    </div>

    {{-- Acciones rápidas --}}
    <div class="bg-white rounded-lg p-5" style="border: 1px solid var(--color-border)">
        <p class="text-[11px] font-semibold uppercase tracking-wide mb-4" style="color: var(--color-muted)">
            Acciones rápidas
        </p>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('expedientes.create') }}" wire:navigate
               class="inline-flex items-center gap-1.5 h-8 px-3 rounded-md text-xs font-medium text-white transition-colors"
               style="background-color: var(--color-primary)"
               x-data
               x-on:mouseenter="$el.style.backgroundColor = 'var(--color-primary-hover)'"
               x-on:mouseleave="$el.style.backgroundColor = 'var(--color-primary)'">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo expediente
            </a>
            <a href="{{ route('documentos.upload') }}" wire:navigate
               class="inline-flex items-center gap-1.5 h-8 px-3 rounded-md text-xs font-medium transition-colors"
               style="border: 1px solid var(--color-border); color: var(--color-text-secondary)"
               x-data
               x-on:mouseenter="$el.style.backgroundColor = '#fafafa'"
               x-on:mouseleave="$el.style.backgroundColor = 'transparent'">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Subir documento
            </a>
            <a href="{{ route('expedientes.index') }}" wire:navigate
               class="inline-flex items-center gap-1.5 h-8 px-3 rounded-md text-xs font-medium transition-colors"
               style="border: 1px solid var(--color-border); color: var(--color-text-secondary)"
               x-data
               x-on:mouseenter="$el.style.backgroundColor = '#fafafa'"
               x-on:mouseleave="$el.style.backgroundColor = 'transparent'">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                Ver expedientes
            </a>
        </div>
    </div>

</div>
