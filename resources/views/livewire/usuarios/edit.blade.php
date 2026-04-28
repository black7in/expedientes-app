<div class="max-w-lg">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-1.5 text-xs mb-6" style="color: var(--color-subtle)">
        <a href="{{ route('usuarios.index') }}" wire:navigate class="hover:text-slate-700 transition-colors">Usuarios</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span style="color: var(--color-text-secondary)">{{ $usuario->nombre }}</span>
    </div>

    <form wire:submit="guardar" class="space-y-5" novalidate>
        <div class="bg-white rounded-lg p-5 space-y-4" style="border: 1px solid var(--color-border)">
            <h2 class="text-xs font-semibold uppercase tracking-wide" style="color: var(--color-muted)">Datos del usuario</h2>

            {{-- Nombre --}}
            <div class="space-y-1.5">
                <label class="text-xs font-medium text-slate-700">
                    Nombre completo <span class="text-red-500">*</span>
                </label>
                <input
                    wire:model="nombre"
                    type="text"
                    placeholder="Ej: Juan Pérez"
                    class="w-full h-8 px-3 text-xs border rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 @error('nombre') border-red-400 @else border-slate-200 @enderror"
                >
                @error('nombre') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Correo --}}
            <div class="space-y-1.5">
                <label class="text-xs font-medium text-slate-700">
                    Correo electrónico <span class="text-red-500">*</span>
                </label>
                <input
                    wire:model="correo"
                    type="email"
                    placeholder="Ej: juan@estudio.com"
                    class="w-full h-8 px-3 text-xs border rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 @error('correo') border-red-400 @else border-slate-200 @enderror"
                >
                @error('correo') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Contraseña --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-medium text-slate-700">
                        Nueva contraseña
                        <span class="font-normal ml-1" style="color: var(--color-subtle)">(opcional)</span>
                    </label>
                    <input
                        wire:model="password"
                        type="password"
                        placeholder="Mínimo 8 caracteres"
                        class="w-full h-8 px-3 text-xs border rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 @error('password') border-red-400 @else border-slate-200 @enderror"
                    >
                    @error('password') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-medium text-slate-700">Confirmar contraseña</label>
                    <input
                        wire:model="password_confirmation"
                        type="password"
                        placeholder="Repetir contraseña"
                        class="w-full h-8 px-3 text-xs border border-slate-200 rounded-md focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400"
                    >
                </div>
            </div>

            {{-- Rol y activo --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-medium text-slate-700">
                        Rol <span class="text-red-500">*</span>
                    </label>
                    <select
                        wire:model="rol"
                        class="w-full h-8 px-3 text-xs border rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:border-slate-400 @error('rol') border-red-400 @else border-slate-200 @enderror"
                    >
                        <option value="abogado">Abogado</option>
                        <option value="pasante">Pasante</option>
                        <option value="administrador">Administrador</option>
                    </select>
                    @error('rol') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-medium text-slate-700">Estado</label>
                    <select
                        wire:model="activo"
                        class="w-full h-8 px-3 text-xs border border-slate-200 rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-slate-900/10"
                    >
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="flex items-center gap-2 justify-end">
            <a href="{{ route('usuarios.index') }}" wire:navigate
               class="h-8 px-4 text-xs border border-slate-200 rounded-md hover:bg-slate-50 transition-colors inline-flex items-center"
               style="color: var(--color-muted)">
                Cancelar
            </a>
            <button type="submit"
                    wire:loading.attr="disabled"
                    class="h-8 px-4 text-white text-xs font-medium rounded-md transition-colors disabled:opacity-50 inline-flex items-center gap-1.5"
                    style="background-color: var(--color-primary)"
                    onmouseover="this.style.backgroundColor='var(--color-primary-hover)'"
                    onmouseout="this.style.backgroundColor='var(--color-primary)'">
                <span wire:loading.remove>Guardar cambios</span>
                <span wire:loading class="flex items-center gap-1.5">
                    <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Guardando...
                </span>
            </button>
        </div>
    </form>
</div>
