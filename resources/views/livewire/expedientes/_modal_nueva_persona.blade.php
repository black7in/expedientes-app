{{-- Modal nueva persona — incluido en create.blade.php y edit.blade.php --}}
@if($showModalPersona)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
         style="background-color: rgba(0,0,0,0.45)">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-sm"
             style="border: 1px solid var(--color-border)">

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4"
                 style="border-bottom: 1px solid var(--color-border)">
                <div>
                    <h3 class="text-sm font-semibold" style="color: var(--color-text)">Nueva persona</h3>
                    <p class="text-[11px] mt-0.5" style="color: var(--color-muted)">
                        Se registrará y agregará como parte del expediente
                    </p>
                </div>
                <button wire:click="cerrarModalPersona" type="button"
                        class="p-1 rounded-md hover:bg-slate-100 transition-colors"
                        style="color: var(--color-muted)">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Cuerpo --}}
            <div class="px-5 py-4 space-y-3">

                {{-- Tipo --}}
                <div class="flex gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model="mpTipo" value="natural" class="w-3.5 h-3.5 accent-emerald-600">
                        <span class="text-xs" style="color: var(--color-text)">Persona natural</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" wire:model="mpTipo" value="juridica" class="w-3.5 h-3.5 accent-emerald-600">
                        <span class="text-xs" style="color: var(--color-text)">Persona jurídica</span>
                    </label>
                </div>

                {{-- Nombre --}}
                <div class="space-y-1">
                    <label class="text-xs font-medium" style="color: var(--color-text)">
                        Nombre completo / Razón social <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="mpNombre" type="text"
                           placeholder="Ej: Juan Carlos Pérez López"
                           class="w-full h-8 px-3 text-xs rounded-md @error('mpNombre') border-red-400 @enderror"
                           style="border: 1px solid var(--color-border); outline: none;">
                    @error('mpNombre') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                {{-- CI/NIT --}}
                <div class="space-y-1">
                    <label class="text-xs font-medium" style="color: var(--color-text)">
                        CI / NIT <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="mpCiNit" type="text"
                           placeholder="Ej: 1234567 CB"
                           class="w-full h-8 px-3 text-xs font-mono rounded-md @error('mpCiNit') border-red-400 @enderror"
                           style="border: 1px solid var(--color-border); outline: none;">
                    @error('mpCiNit') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    {{-- Teléfono --}}
                    <div class="space-y-1">
                        <label class="text-xs font-medium" style="color: var(--color-text)">Teléfono</label>
                        <input wire:model="mpTelefono" type="text"
                               placeholder="Ej: 77712345"
                               class="w-full h-8 px-3 text-xs rounded-md"
                               style="border: 1px solid var(--color-border); outline: none;">
                    </div>
                    {{-- Correo --}}
                    <div class="space-y-1">
                        <label class="text-xs font-medium" style="color: var(--color-text)">Correo</label>
                        <input wire:model="mpCorreo" type="email"
                               placeholder="Ej: juan@correo.com"
                               class="w-full h-8 px-3 text-xs rounded-md @error('mpCorreo') border-red-400 @enderror"
                               style="border: 1px solid var(--color-border); outline: none;">
                        @error('mpCorreo') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-2 px-5 py-4"
                 style="border-top: 1px solid var(--color-border)">
                <button wire:click="cerrarModalPersona" type="button"
                        class="h-8 px-4 text-xs rounded-md hover:bg-slate-50 transition-colors"
                        style="border: 1px solid var(--color-border); color: var(--color-text-secondary)">
                    Cancelar
                </button>
                <button wire:click="crearYAgregarPersona" type="button"
                        wire:loading.attr="disabled"
                        class="h-8 px-4 text-xs font-medium text-white rounded-md hover:opacity-90 transition-opacity disabled:opacity-50"
                        style="background-color: var(--color-primary)">
                    <span wire:loading.remove wire:target="crearYAgregarPersona">Crear y agregar</span>
                    <span wire:loading wire:target="crearYAgregarPersona">Guardando...</span>
                </button>
            </div>
        </div>
    </div>
@endif
