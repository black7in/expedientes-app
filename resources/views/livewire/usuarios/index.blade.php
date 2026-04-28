<div>
    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-4 flex items-center gap-2 px-4 py-2.5 rounded-md text-xs font-medium"
             style="background-color: var(--color-primary-light); color: var(--color-primary-text)">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- Toolbar --}}
    <div class="flex items-center justify-between mb-5 gap-4">
        <div class="flex items-center gap-2 flex-1">
            {{-- Búsqueda --}}
            <div class="relative flex-1 max-w-xs">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input
                    wire:model.live.debounce.300ms="busqueda"
                    type="search"
                    placeholder="Buscar por nombre o correo..."
                    class="w-full h-8 pl-9 pr-3 text-xs border border-slate-200 rounded-md bg-white
                           focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400
                           placeholder:text-slate-400"
                >
            </div>

            {{-- Filtro rol --}}
            <select wire:model.live="rol"
                    class="h-8 px-2.5 text-xs border border-slate-200 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10">
                <option value="">Todos los roles</option>
                <option value="administrador">Administrador</option>
                <option value="abogado">Abogado</option>
                <option value="pasante">Pasante</option>
            </select>

            {{-- Filtro activo --}}
            <select wire:model.live="activo"
                    class="h-8 px-2.5 text-xs border border-slate-200 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10">
                <option value="">Todos los estados</option>
                <option value="1">Activos</option>
                <option value="0">Inactivos</option>
            </select>
        </div>

        <a href="{{ route('usuarios.create') }}" wire:navigate
           class="inline-flex items-center gap-1.5 h-8 px-3 text-white text-xs font-medium rounded-md transition-colors flex-shrink-0"
           style="background-color: var(--color-primary);"
           onmouseover="this.style.backgroundColor='var(--color-primary-hover)'"
           onmouseout="this.style.backgroundColor='var(--color-primary)'">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nuevo usuario
        </a>
    </div>

    {{-- Tabla --}}
    <div class="bg-white rounded-lg overflow-hidden" style="border: 1px solid var(--color-border)">
        <table class="w-full text-xs">
            <thead>
                <tr class="bg-white" style="border-bottom: 1px solid var(--color-border)">
                    <th class="text-left px-4 py-2.5 font-semibold text-slate-600 uppercase tracking-wide text-[10px]">Nombre</th>
                    <th class="text-left px-4 py-2.5 font-semibold text-slate-600 uppercase tracking-wide text-[10px]">Correo</th>
                    <th class="text-left px-4 py-2.5 font-semibold text-slate-600 uppercase tracking-wide text-[10px]">Rol</th>
                    <th class="text-left px-4 py-2.5 font-semibold text-slate-600 uppercase tracking-wide text-[10px]">Estado</th>
                    <th class="px-4 py-2.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($usuarios as $usuario)
                    <tr class="hover:bg-slate-50 transition-colors group">
                        <td class="px-4 py-3 font-medium text-slate-900">
                            {{ $usuario->nombre }}
                        </td>
                        <td class="px-4 py-3" style="color: var(--color-muted)">
                            {{ $usuario->correo }}
                        </td>
                        <td class="px-4 py-3">
                            @if($usuario->rol === 'administrador')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-violet-50 text-violet-700 border border-violet-200">
                                    Administrador
                                </span>
                            @elseif($usuario->rol === 'abogado')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                    Abogado
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-slate-600 border border-slate-200">
                                    Pasante
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <button
                                wire:click="toggleActivo('{{ $usuario->id }}')"
                                type="button"
                                title="{{ $usuario->activo ? 'Desactivar usuario' : 'Activar usuario' }}"
                                class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-offset-1"
                                style="{{ $usuario->activo ? 'background-color: var(--color-primary); focus-ring-color: var(--color-primary)' : 'background-color: #d1d5db;' }}"
                            >
                                <span class="pointer-events-none inline-block h-4 w-4 rounded-full bg-white shadow transform transition duration-200 {{ $usuario->activo ? 'translate-x-4' : 'translate-x-0' }}"></span>
                            </button>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('usuarios.edit', $usuario) }}" wire:navigate
                               class="opacity-0 group-hover:opacity-100 inline-flex items-center gap-1 text-slate-500 hover:text-slate-900 transition-all text-xs">
                                Editar
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-12 text-center">
                            <svg class="w-8 h-8 mx-auto mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <p class="text-xs" style="color: var(--color-subtle)">No se encontraron usuarios</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($usuarios->hasPages())
            <div class="px-4 py-3" style="border-top: 1px solid var(--color-border)">
                {{ $usuarios->links() }}
            </div>
        @endif
    </div>

    {{-- Loading overlay --}}
    <div wire:loading.delay class="fixed inset-0 bg-white/30 backdrop-blur-[1px] z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg px-4 py-2.5 flex items-center gap-2 shadow-sm text-xs text-slate-600"
             style="border: 1px solid var(--color-border)">
            <svg class="animate-spin w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            Cargando...
        </div>
    </div>
</div>
