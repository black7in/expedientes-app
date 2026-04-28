<div>

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-base font-semibold" style="color: var(--color-text)">Partes procesales</h2>
            <p class="text-xs mt-0.5" style="color: var(--color-muted)">Personas naturales y jurídicas registradas en el sistema</p>
        </div>
        <a href="{{ route('personas.create') }}" wire:navigate
           class="inline-flex items-center gap-1.5 h-8 px-3 text-xs font-medium text-white rounded-md hover:opacity-90 transition-opacity"
           style="background-color: var(--color-primary)">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva persona
        </a>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-5 px-4 py-3 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    {{-- Filtros --}}
    <div class="flex items-center gap-3 mb-4">
        <div class="relative flex-1 max-w-sm">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 pointer-events-none" style="color: var(--color-muted)"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="buscar"
                   type="text"
                   placeholder="Buscar por nombre, CI/NIT o correo..."
                   class="w-full h-8 pl-9 pr-3 text-xs rounded-md"
                   style="border: 1px solid var(--color-border); color: var(--color-text); background: white;
                          outline: none;">
        </div>
        <select wire:model.live="filtroTipo"
                class="h-8 px-3 text-xs rounded-md bg-white"
                style="border: 1px solid var(--color-border); color: var(--color-text)">
            <option value="">Todos los tipos</option>
            <option value="natural">Persona natural</option>
            <option value="juridica">Persona jurídica</option>
        </select>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-lg overflow-hidden" style="border: 1px solid var(--color-border)">
        <table class="w-full text-xs">
            <thead>
                <tr style="border-bottom: 1px solid var(--color-border); background-color: var(--color-surface)">
                    <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wide" style="color: var(--color-muted)">
                        Nombre
                    </th>
                    <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wide" style="color: var(--color-muted)">
                        CI / NIT
                    </th>
                    <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wide" style="color: var(--color-muted)">
                        Tipo
                    </th>
                    <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wide" style="color: var(--color-muted)">
                        Contacto
                    </th>
                    <th class="px-4 py-3 text-left text-[10px] font-semibold uppercase tracking-wide" style="color: var(--color-muted)">
                        Expedientes
                    </th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y" style="border-color: var(--color-border)">
                @forelse($personas as $persona)
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="px-4 py-3">
                            <span class="font-medium" style="color: var(--color-text)">{{ $persona->nombre_completo }}</span>
                        </td>
                        <td class="px-4 py-3 font-mono" style="color: var(--color-text-secondary)">
                            {{ $persona->ci_nit }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium ring-1 ring-inset
                                {{ $persona->tipo_persona === 'natural'
                                    ? 'bg-blue-50 text-blue-700 ring-blue-600/20'
                                    : 'bg-violet-50 text-violet-700 ring-violet-600/20' }}">
                                {{ $persona->tipo_persona === 'natural' ? 'Natural' : 'Jurídica' }}
                            </span>
                        </td>
                        <td class="px-4 py-3" style="color: var(--color-muted)">
                            <div class="space-y-0.5">
                                @if($persona->telefono)
                                    <p>{{ $persona->telefono }}</p>
                                @endif
                                @if($persona->correo)
                                    <p>{{ $persona->correo }}</p>
                                @endif
                                @if(!$persona->telefono && !$persona->correo)
                                    <span class="text-slate-300">—</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            @php $totalPartes = $persona->partes()->count(); @endphp
                            @if($totalPartes > 0)
                                <span class="text-xs font-medium" style="color: var(--color-text-secondary)">
                                    {{ $totalPartes }} expediente{{ $totalPartes !== 1 ? 's' : '' }}
                                </span>
                            @else
                                <span style="color: var(--color-muted)">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('personas.edit', $persona) }}" wire:navigate
                               class="text-[11px] font-medium hover:underline" style="color: var(--color-primary)">
                                Editar
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <p class="text-xs" style="color: var(--color-muted)">No se encontraron personas</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Paginación --}}
    @if($personas->hasPages())
        <div class="mt-4">
            {{ $personas->links() }}
        </div>
    @endif

</div>
