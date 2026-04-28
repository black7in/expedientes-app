<div>
    {{-- Toolbar --}}
    <div class="flex items-center justify-between gap-4 mb-5">
        <div class="flex items-center gap-2 flex-1">
            <div class="relative flex-1 max-w-xs">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 pointer-events-none" style="color: var(--color-subtle)"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input wire:model.live.debounce.300ms="busqueda" type="search"
                       placeholder="Buscar por nombre..."
                       class="w-full h-8 pl-9 pr-3 text-xs rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-offset-0"
                       style="border: 1px solid var(--color-border)">
            </div>

            <select wire:model.live="formato"
                    class="h-8 px-2.5 text-xs rounded-md bg-white focus:outline-none"
                    style="border: 1px solid var(--color-border)">
                <option value="">Formato</option>
                <option value="pdf">PDF</option>
                <option value="docx">DOCX</option>
            </select>

            <select wire:model.live="estado"
                    class="h-8 px-2.5 text-xs rounded-md bg-white focus:outline-none"
                    style="border: 1px solid var(--color-border)">
                <option value="">Estado</option>
                <option value="pendiente">Pendiente</option>
                <option value="procesado">Procesado</option>
                <option value="error">Error</option>
            </select>
        </div>

        <a href="{{ route('documentos.upload') }}" wire:navigate
           class="h-8 px-3 text-white text-xs font-medium rounded-md inline-flex items-center gap-1.5 flex-shrink-0"
           style="background-color: var(--color-primary)">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Subir documento
        </a>
    </div>

    <div class="bg-white rounded-lg overflow-hidden" style="border: 1px solid var(--color-border)">
        <table class="w-full text-xs">
            <thead>
                <tr style="border-bottom: 1px solid var(--color-border); background-color: #fafafa">
                    <th class="text-left px-4 py-2.5 font-semibold uppercase tracking-wide text-[10px]" style="color: var(--color-muted)">Archivo</th>
                    <th class="text-left px-4 py-2.5 font-semibold uppercase tracking-wide text-[10px]" style="color: var(--color-muted)">Tipo</th>
                    <th class="text-left px-4 py-2.5 font-semibold uppercase tracking-wide text-[10px]" style="color: var(--color-muted)">Expediente</th>
                    <th class="text-left px-4 py-2.5 font-semibold uppercase tracking-wide text-[10px]" style="color: var(--color-muted)">Estado</th>
                    <th class="text-left px-4 py-2.5 font-semibold uppercase tracking-wide text-[10px]" style="color: var(--color-muted)">Subido por</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: var(--color-border)">
                @forelse($documentos as $doc)
                    <tr class="group hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded flex items-center justify-center flex-shrink-0 text-[10px] font-bold"
                                     style="{{ $doc->formato === 'pdf' ? 'background-color:#fee2e2;color:#b91c1c' : 'background-color:#dbeafe;color:#1d4ed8' }}">
                                    {{ strtoupper($doc->formato) }}
                                </div>
                                <span class="font-mono text-[11px]" style="color: var(--color-text)">
                                    {{ Str::limit($doc->nombre_archivo, 35) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-4 py-3 capitalize" style="color: var(--color-text-secondary)">{{ $doc->tipo_documento }}</td>
                        <td class="px-4 py-3" style="color: var(--color-text-secondary)">
                            @if($doc->expediente)
                                <a href="{{ route('expedientes.show', $doc->expediente) }}" wire:navigate class="hover:underline">
                                    {{ $doc->expediente->numero_expediente ?? 'Sin número' }}
                                </a>
                            @else
                                <span style="color: var(--color-subtle)">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $estadoClases = match($doc->estado_extraccion) {
                                    'procesado' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                                    'error'     => 'bg-red-50 text-red-700 ring-red-600/20',
                                    default     => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                                };
                            @endphp
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium ring-1 ring-inset {{ $estadoClases }}">
                                {{ ucfirst($doc->estado_extraccion) }}
                            </span>
                        </td>
                        <td class="px-4 py-3" style="color: var(--color-muted)">{{ $doc->usuario->nombre ?? '—' }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('documentos.show', $doc) }}" wire:navigate
                               class="opacity-0 group-hover:opacity-100 inline-flex items-center gap-1 text-xs transition-all"
                               style="color: var(--color-muted)">
                                Ver
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center text-xs" style="color: var(--color-subtle)">
                            No hay documentos cargados todavía
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($documentos->hasPages())
            <div class="px-4 py-3" style="border-top: 1px solid var(--color-border)">
                {{ $documentos->links() }}
            </div>
        @endif
    </div>

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
