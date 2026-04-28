<div class="max-w-xl">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-1.5 text-xs mb-6" style="color: var(--color-muted)">
        <a href="{{ route('personas.index') }}" wire:navigate class="hover:underline" style="color: var(--color-muted)">Partes procesales</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="truncate max-w-xs" style="color: var(--color-text-secondary)">{{ $persona->nombre_completo }}</span>
    </div>

    @if(session('success'))
        <div class="mb-5 px-4 py-3 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit="guardar" class="space-y-5" novalidate>

        <div class="bg-white rounded-lg p-6 space-y-4" style="border: 1px solid var(--color-border)">
            <h2 class="text-xs font-semibold uppercase tracking-wide" style="color: var(--color-muted)">Datos de la persona</h2>

            {{-- Tipo --}}
            <div class="space-y-1.5">
                <label class="text-xs font-medium" style="color: var(--color-text)">Tipo de persona <span class="text-red-500">*</span></label>
                <div class="flex gap-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model="tipo_persona" value="natural" class="w-3.5 h-3.5 accent-emerald-600">
                        <span class="text-xs" style="color: var(--color-text)">Persona natural</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model="tipo_persona" value="juridica" class="w-3.5 h-3.5 accent-emerald-600">
                        <span class="text-xs" style="color: var(--color-text)">Persona jurídica</span>
                    </label>
                </div>
            </div>

            {{-- Nombre --}}
            <div class="space-y-1.5">
                <label class="text-xs font-medium" style="color: var(--color-text)">
                    Nombre completo / Razón social <span class="text-red-500">*</span>
                </label>
                <input wire:model="nombre_completo" type="text"
                       class="w-full h-8 px-3 text-xs rounded-md @error('nombre_completo') border-red-400 @enderror"
                       style="border: 1px solid var(--color-border); color: var(--color-text); outline: none;">
                @error('nombre_completo') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- CI / NIT --}}
            <div class="space-y-1.5">
                <label class="text-xs font-medium" style="color: var(--color-text)">CI / NIT <span class="text-red-500">*</span></label>
                <input wire:model="ci_nit" type="text"
                       class="w-full h-8 px-3 text-xs font-mono rounded-md @error('ci_nit') border-red-400 @enderror"
                       style="border: 1px solid var(--color-border); color: var(--color-text); outline: none;">
                @error('ci_nit') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Teléfono --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-medium" style="color: var(--color-text)">
                        Teléfono
                        <span class="font-normal ml-1" style="color: var(--color-muted)">(opcional)</span>
                    </label>
                    <input wire:model="telefono" type="text"
                           class="w-full h-8 px-3 text-xs rounded-md @error('telefono') border-red-400 @enderror"
                           style="border: 1px solid var(--color-border); color: var(--color-text); outline: none;">
                    @error('telefono') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- Correo --}}
                <div class="space-y-1.5">
                    <label class="text-xs font-medium" style="color: var(--color-text)">
                        Correo
                        <span class="font-normal ml-1" style="color: var(--color-muted)">(opcional)</span>
                    </label>
                    <input wire:model="correo" type="email"
                           class="w-full h-8 px-3 text-xs rounded-md @error('correo') border-red-400 @enderror"
                           style="border: 1px solid var(--color-border); color: var(--color-text); outline: none;">
                    @error('correo') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Dirección --}}
            <div class="space-y-1.5">
                <label class="text-xs font-medium" style="color: var(--color-text)">
                    Dirección
                    <span class="font-normal ml-1" style="color: var(--color-muted)">(opcional)</span>
                </label>
                <input wire:model="direccion" type="text"
                       class="w-full h-8 px-3 text-xs rounded-md @error('direccion') border-red-400 @enderror"
                       style="border: 1px solid var(--color-border); color: var(--color-text); outline: none;">
                @error('direccion') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Expedientes vinculados (info) --}}
        @php $totalExpedientes = $persona->partes()->count(); @endphp
        @if($totalExpedientes > 0)
            <div class="px-4 py-3 rounded-lg text-xs" style="background: var(--color-surface); border: 1px solid var(--color-border); color: var(--color-muted)">
                Esta persona aparece como parte en <strong style="color: var(--color-text)">{{ $totalExpedientes }}</strong>
                expediente{{ $totalExpedientes !== 1 ? 's' : '' }}.
            </div>
        @endif

        {{-- Acciones --}}
        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('personas.index') }}" wire:navigate
               class="h-8 px-4 text-xs rounded-md inline-flex items-center transition-colors hover:bg-slate-50"
               style="border: 1px solid var(--color-border); color: var(--color-text-secondary)">
                Cancelar
            </a>
            <button type="submit"
                    wire:loading.attr="disabled"
                    class="h-8 px-4 text-xs font-medium text-white rounded-md hover:opacity-90 transition-opacity disabled:opacity-50 inline-flex items-center gap-1.5"
                    style="background-color: var(--color-primary)">
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
