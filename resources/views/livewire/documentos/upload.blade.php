<div class="max-w-2xl">

    <div class="flex items-center gap-1.5 text-xs mb-6" style="color: var(--color-subtle)">
        <a href="{{ route('documentos.index') }}" wire:navigate class="hover:underline" style="color: var(--color-muted)">Documentos</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
        <span>Subir documento</span>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded-md bg-emerald-50 text-emerald-700 text-xs border border-emerald-200">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit="subir" class="space-y-4" novalidate>

        {{-- Archivo --}}
        <div class="bg-white rounded-lg p-6 space-y-4" style="border: 1px solid var(--color-border)">
            <h2 class="text-xs font-semibold uppercase tracking-wide" style="color: var(--color-muted)">Archivo</h2>

            <div class="space-y-1.5">
                <label class="text-xs font-medium" style="color: var(--color-text-secondary)">
                    Documento <span class="text-red-500">*</span>
                </label>

                <div wire:loading.remove wire:target="archivo">
                    <label class="flex flex-col items-center justify-center w-full h-32 rounded-md cursor-pointer transition-colors hover:bg-slate-50"
                           style="border: 2px dashed var(--color-border-strong)">
                        <svg class="w-6 h-6 mb-2" style="color: var(--color-subtle)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        @if($archivo)
                            <span class="text-xs font-medium" style="color: var(--color-text)">{{ $archivo->getClientOriginalName() }}</span>
                            <span class="text-[11px] mt-0.5" style="color: var(--color-muted)">
                                {{ number_format($archivo->getSize() / 1024, 1) }} KB
                            </span>
                        @else
                            <span class="text-xs" style="color: var(--color-muted)">Arrastrá o hacé clic para seleccionar</span>
                            <span class="text-[11px] mt-0.5" style="color: var(--color-subtle)">PDF o DOCX · máx. 50MB</span>
                        @endif
                        <input wire:model="archivo" type="file" accept=".pdf,.docx" class="hidden">
                    </label>
                </div>

                <div wire:loading wire:target="archivo" class="flex items-center gap-2 py-4 justify-center">
                    <svg class="animate-spin w-4 h-4" style="color: var(--color-primary)" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    <span class="text-xs" style="color: var(--color-muted)">Subiendo...</span>
                </div>

                @error('archivo') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
            </div>

            {{-- Tipo documento --}}
            <div class="space-y-1.5">
                <label class="text-xs font-medium" style="color: var(--color-text-secondary)">
                    Tipo de documento <span class="text-red-500">*</span>
                </label>
                <select wire:model="tipo_documento"
                        class="w-full h-8 px-3 text-xs rounded-md bg-white focus:outline-none focus:ring-2 focus:ring-offset-0"
                        style="border: 1px solid var(--color-border)">
                    <option value="demanda">Demanda</option>
                    <option value="sentencia">Sentencia</option>
                    <option value="memorial">Memorial</option>
                    <option value="contrato">Contrato</option>
                    <option value="notificacion">Notificación</option>
                    <option value="otro">Otro</option>
                </select>
            </div>
        </div>

        {{-- Vincular expediente (PB-7) --}}
        <div class="bg-white rounded-lg p-6 space-y-4" style="border: 1px solid var(--color-border)">
            <div class="flex items-center justify-between">
                <h2 class="text-xs font-semibold uppercase tracking-wide" style="color: var(--color-muted)">Vincular a expediente</h2>
                <span class="text-[11px]" style="color: var(--color-subtle)">Opcional</span>
            </div>

            @if($expedienteSeleccionado)
                <div class="flex items-center justify-between px-3 py-2 rounded-md" style="background-color: var(--color-primary-light)">
                    <div>
                        <p class="text-xs font-medium" style="color: var(--color-primary-text)">{{ $expedienteLabel }}</p>
                    </div>
                    <button type="button" wire:click="limpiarExpediente"
                            class="text-xs" style="color: var(--color-primary-text)">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            @else
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 pointer-events-none" style="color: var(--color-subtle)"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input wire:model.live.debounce.300ms="buscarExpediente"
                           type="text"
                           placeholder="Buscar por nro. expediente o parte..."
                           class="w-full h-8 pl-9 pr-3 text-xs rounded-md focus:outline-none focus:ring-2 focus:ring-offset-0"
                           style="border: 1px solid var(--color-border)">

                    @if(count($resultadosExpediente))
                        <div class="absolute z-20 top-full mt-1 w-full bg-white rounded-md shadow-lg overflow-hidden"
                             style="border: 1px solid var(--color-border)">
                            @foreach($resultadosExpediente as $exp)
                                <button type="button"
                                        wire:click="seleccionarExpediente('{{ $exp['id'] }}', '{{ addslashes($exp['label']) }}')"
                                        class="w-full text-left px-3 py-2 hover:bg-slate-50 transition-colors">
                                    <p class="text-xs font-medium" style="color: var(--color-text)">{{ $exp['label'] }}</p>
                                    <p class="text-[11px]" style="color: var(--color-subtle)">{{ $exp['partes'] }}</p>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Acciones --}}
        <div class="flex items-center gap-2 justify-end">
            <a href="{{ route('documentos.index') }}" wire:navigate
               class="h-8 px-4 text-xs rounded-md inline-flex items-center transition-colors"
               style="border: 1px solid var(--color-border); color: var(--color-text-secondary)">
                Cancelar
            </a>
            <button type="submit" wire:loading.attr="disabled"
                    class="h-8 px-4 text-white text-xs font-medium rounded-md transition-colors disabled:opacity-50 inline-flex items-center gap-1.5"
                    style="background-color: var(--color-primary)">
                <span wire:loading.remove>Subir documento</span>
                <span wire:loading class="flex items-center gap-1.5">
                    <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Subiendo...
                </span>
            </button>
        </div>
    </form>
</div>
