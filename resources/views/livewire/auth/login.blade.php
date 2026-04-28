<div>
    <div class="mb-7">
        <h2 class="text-2xl font-semibold tracking-tight text-slate-900">Iniciar sesión</h2>
        <p class="text-slate-500 text-sm mt-1">Ingresá tus credenciales para continuar</p>
    </div>

    <form wire:submit="login" class="space-y-4" novalidate>

        <div class="space-y-1.5">
            <label for="correo" class="text-xs font-medium text-slate-700 uppercase tracking-wide">
                Correo electrónico
            </label>
            <input
                wire:model="correo"
                type="email"
                id="correo"
                autocomplete="email"
                placeholder="tu@correo.bo"
                class="w-full h-9 px-3 rounded-md border bg-white text-sm text-slate-900 placeholder:text-slate-400
                       transition-colors outline-none
                       @error('correo')
                           border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-500/20
                       @else
                           border-slate-200 focus:border-slate-400 focus:ring-2 focus:ring-slate-900/10
                       @enderror"
            >
            @error('correo')
                <p class="text-xs text-red-600 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="space-y-1.5">
            <label for="password" class="text-xs font-medium text-slate-700 uppercase tracking-wide">
                Contraseña
            </label>
            <input
                wire:model="password"
                type="password"
                id="password"
                autocomplete="current-password"
                placeholder="••••••••"
                class="w-full h-9 px-3 rounded-md border bg-white text-sm text-slate-900 placeholder:text-slate-400
                       transition-colors outline-none
                       @error('password')
                           border-red-400 focus:border-red-500 focus:ring-2 focus:ring-red-500/20
                       @else
                           border-slate-200 focus:border-slate-400 focus:ring-2 focus:ring-slate-900/10
                       @enderror"
            >
            @error('password')
                <p class="text-xs text-red-600 flex items-center gap-1">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    {{ $message }}
                </p>
            @enderror
        </div>

        <div class="flex items-center gap-2 pt-1">
            <input
                wire:model="recordar"
                type="checkbox"
                id="recordar"
                class="w-3.5 h-3.5 rounded border-slate-300 text-slate-900 focus:ring-slate-900/20"
            >
            <label for="recordar" class="text-xs text-slate-600 select-none">Recordar sesión</label>
        </div>

        <button
            type="submit"
            wire:loading.attr="disabled"
            class="w-full h-9 bg-slate-900 hover:bg-slate-800 text-white text-sm font-medium rounded-md
                   transition-colors focus:outline-none focus:ring-2 focus:ring-slate-900/30 focus:ring-offset-1
                   disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 mt-2"
        >
            <span wire:loading.remove>Ingresar</span>
            <span wire:loading class="flex items-center gap-2">
                <svg class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                Verificando...
            </span>
        </button>

    </form>
</div>
