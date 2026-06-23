<div @if(!in_array($documento->estado_extraccion, ['pendiente_revision', 'confirmado', 'error'])) wire:poll.3000ms="refrescar" @endif>

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-1.5 text-xs mb-5" style="color: var(--color-muted)">
        <a href="{{ route('documentos.index') }}" wire:navigate class="hover:underline" style="color: var(--color-muted)">Documentos</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
        <span style="color: var(--color-text-secondary)" class="truncate max-w-xs">{{ $documento->nombre_archivo }}</span>
    </div>

    {{-- Banners de procesamiento --}}
    @if($documento->isPendiente())
        <div class="flex items-center gap-3 px-4 py-3 rounded-lg mb-5 text-sm"
             style="background-color:#fffbeb;border:1px solid #fde68a;color:#92400e">
            <svg class="w-4 h-4 animate-spin flex-shrink-0" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
            </svg>
            <span class="font-medium">Extrayendo texto del documento...</span>
            <span class="text-xs opacity-70">Esta página se actualiza automáticamente</span>
        </div>
    @elseif($documento->estado_extraccion === 'procesado')
        <div class="flex items-center gap-3 px-4 py-3 rounded-lg mb-5 text-sm"
             style="background-color:#eff6ff;border:1px solid #bfdbfe;color:#1e40af">
            <svg class="w-4 h-4 animate-spin flex-shrink-0" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
            </svg>
            <span class="font-medium">Detectando entidades sensibles (NER)...</span>
            <span class="text-xs opacity-70">Esta página se actualiza automáticamente</span>
        </div>
    @elseif($documento->isPendienteRevision())
        <div class="flex items-center gap-3 px-4 py-3 rounded-lg mb-5 text-sm"
             style="background-color:#fefce8;border:1px solid #fde047;color:#713f12">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="font-medium">Revisá las entidades detectadas y confirmá la anonimización antes de usar este documento en generaciones.</span>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4 mb-6">
        <div class="flex items-center gap-3 min-w-0">
            <div class="w-8 h-8 rounded-md flex items-center justify-center flex-shrink-0 text-xs font-bold"
                 style="{{ $documento->formato === 'pdf' ? 'background-color:#fee2e2;color:#b91c1c' : 'background-color:#dbeafe;color:#1d4ed8' }}">
                {{ strtoupper($documento->formato) }}
            </div>
            <div class="min-w-0">
                <h2 class="text-sm font-semibold font-mono truncate" style="color: var(--color-text)">
                    {{ $documento->nombre_archivo }}
                </h2>
                <div class="flex items-center gap-2 mt-0.5">
                    @php
                        $estadoBadge = match($documento->estado_extraccion) {
                            'confirmado'         => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                            'pendiente_revision' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                            'procesado'          => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                            'error'              => 'bg-red-50 text-red-700 ring-red-600/20',
                            default              => 'bg-amber-50 text-amber-700 ring-amber-600/20',
                        };
                        $estadoLabel = match($documento->estado_extraccion) {
                            'confirmado'         => 'Confirmado',
                            'pendiente_revision' => 'Pendiente revisión',
                            'procesado'          => 'Procesando NER...',
                            'pendiente'          => 'Pendiente',
                            'error'              => 'Error',
                            default              => ucfirst($documento->estado_extraccion),
                        };
                    @endphp
                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium ring-1 ring-inset {{ $estadoBadge }}">
                        {{ $estadoLabel }}
                    </span>
                    <span class="text-[11px] capitalize" style="color: var(--color-muted)">{{ $documento->tipo_documento }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Meta row --}}
    <div class="flex flex-wrap items-center gap-x-5 gap-y-1.5 text-xs mb-6" style="color: var(--color-muted)">
        @if($documento->usuario)
            <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                {{ $documento->usuario->nombre }}
            </span>
            <span style="color: var(--color-border-strong)">|</span>
        @endif
        <span class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            {{ $documento->created_at->format('d/m/Y H:i') }}
        </span>
        @if($documento->expediente)
            <span style="color: var(--color-border-strong)">|</span>
            <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <a href="{{ route('expedientes.show', $documento->expediente) }}" wire:navigate
                   class="hover:underline font-mono" style="color: var(--color-primary)">
                    Exp. {{ $documento->expediente->numero_expediente ?? 'Sin número' }}
                </a>
            </span>
        @endif
    </div>

    {{-- Layout principal --}}
    <div class="flex gap-5 items-start">

        {{-- LEFT — Entidades --}}
        <div class="w-72 flex-shrink-0 space-y-4">

            {{-- Panel entidades --}}
            <div class="bg-white rounded-lg p-4" style="border: 1px solid var(--color-border)">
                <h3 class="text-xs font-semibold uppercase tracking-wide mb-3" style="color: var(--color-muted)">
                    Entidades detectadas
                </h3>

                @if($documento->isRevisable() && !empty($documento->entidades))
                    @php $entidades = $documento->entidades; @endphp

                    <div class="space-y-3">
                        @foreach($entidades as $ent)
                            @php
                                $score      = $ent['score'] ?? 0;
                                $scoreColor = $score >= 0.90 ? '#166534' : ($score >= 0.75 ? '#854d0e' : '#991b1b');
                                $scoreBg    = $score >= 0.90 ? '#dcfce7' : ($score >= 0.75 ? '#fef9c3' : '#fee2e2');
                                $confirmado = $ent['confirmado'] ?? false;
                                $color      = $colores[$ent['tipo']] ?? '#f3f4f6';
                                $esPer      = $ent['tipo'] === 'PER';
                                $rolActual  = $ent['rol'] ?? null;
                            @endphp

                            <div x-data="{ editandoTipo: false, editandoRol: false }"
                                 class="rounded-md p-2.5"
                                 style="background-color: {{ $color }}40; border: 1px solid {{ $color }}">

                                {{-- Fila superior: placeholder + score --}}
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[10px] font-mono font-semibold" style="color: var(--color-text)">
                                        {{ $ent['placeholder'] }}
                                    </span>
                                    <div class="flex items-center gap-1.5">
                                        <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded-full"
                                              style="background-color: {{ $scoreBg }}; color: {{ $scoreColor }}">
                                            {{ number_format($score * 100) }}%
                                        </span>
                                        @if($confirmado)
                                            <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </div>
                                </div>

                                {{-- Texto detectado --}}
                                <p class="text-[11px] truncate mb-2" style="color: var(--color-text-secondary)"
                                   title="{{ $ent['texto'] }}">
                                    {{ \Illuminate\Support\Str::limit($ent['texto'], 30) }}
                                </p>

                                {{-- Selector de tipo --}}
                                <div x-show="editandoTipo" x-cloak class="mb-2">
                                    <select @change="$wire.cambiarTipo('{{ $ent['id'] }}', $event.target.value); editandoTipo = false"
                                            class="w-full text-[10px] rounded border px-1.5 py-1"
                                            style="border-color: var(--color-border); color: var(--color-text)">
                                        @foreach($tiposDisponibles as $tipo)
                                            <option value="{{ $tipo }}" {{ $ent['tipo'] === $tipo ? 'selected' : '' }}>{{ $tipo }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Selector de rol (solo PER) --}}
                                @if($esPer)
                                    <div x-show="editandoRol" x-cloak class="mb-2">
                                        <select @change="$wire.asignarRol('{{ $ent['id'] }}', $event.target.value); editandoRol = false"
                                                class="w-full text-[10px] rounded border px-1.5 py-1"
                                                style="border-color: var(--color-border); color: var(--color-text)">
                                            <option value="">— Seleccionar rol —</option>
                                            @foreach($rolesPer as $rol)
                                                <option value="{{ $rol }}" {{ $rolActual === $rol ? 'selected' : '' }}>{{ $rol }}</option>
                                            @endforeach
                                            <option value="OTRO" {{ !$rolActual ? 'selected' : '' }}>OTRO (sin rol)</option>
                                        </select>
                                    </div>
                                @endif

                                {{-- Acciones --}}
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @if(!$confirmado)
                                        <button wire:click="confirmarEntidad('{{ $ent['id'] }}')"
                                                class="text-[10px] px-2 py-0.5 rounded font-medium hover:opacity-80"
                                                style="background-color: #dcfce7; color: #166534">
                                            Confirmar
                                        </button>
                                    @endif
                                    <button @click="editandoTipo = !editandoTipo; editandoRol = false"
                                            class="text-[10px] px-2 py-0.5 rounded hover:opacity-80"
                                            style="background-color: var(--color-border); color: var(--color-text-secondary)">
                                        Tipo
                                    </button>
                                    @if($esPer)
                                        <button @click="editandoRol = !editandoRol; editandoTipo = false"
                                                class="text-[10px] px-2 py-0.5 rounded hover:opacity-80"
                                                style="background-color: #e0e7ff; color: #3730a3">
                                            Rol
                                        </button>
                                    @endif
                                    <button wire:click="eliminarEntidad('{{ $ent['id'] }}')"
                                            wire:confirm="¿Eliminar esta entidad como falso positivo?"
                                            class="text-[10px] px-2 py-0.5 rounded hover:opacity-80"
                                            style="background-color: #fee2e2; color: #991b1b">
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        @endforeach

                        <p class="text-[10px] pt-1" style="color: var(--color-muted); border-top: 1px solid var(--color-border)">
                            {{ count($entidades) }} entidades ·
                            {{ count(array_filter($entidades, fn($e) => $e['confirmado'] ?? false)) }} confirmadas
                        </p>
                    </div>

                @elseif($documento->isRevisable())
                    <div class="flex items-center gap-2 py-2">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0" style="background-color:#d1fae5">
                            <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <p class="text-xs text-emerald-700">Sin entidades detectadas</p>
                    </div>

                @elseif($documento->isPendiente() || $documento->estado_extraccion === 'procesado')
                    <div class="flex items-center gap-2 py-2">
                        <div class="w-5 h-5 rounded-full bg-amber-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-amber-600 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                            </svg>
                        </div>
                        <p class="text-xs text-amber-700">Procesando...</p>
                    </div>

                @else
                    <div class="flex items-center gap-2 py-2">
                        <div class="w-5 h-5 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                            <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-xs text-red-700">Error en el procesamiento</p>
                    </div>
                @endif

                {{-- Botón confirmar anonimización --}}
                @if($documento->isPendienteRevision())
                    <div class="mt-3 pt-3" style="border-top: 1px solid var(--color-border)">
                        <button wire:click="confirmarAnonimizacion"
                                wire:confirm="¿Confirmás que revisaste las entidades y la anonimización es correcta?"
                                class="w-full text-xs px-3 py-2 rounded-md font-semibold hover:opacity-90 transition-opacity"
                                style="background-color: var(--color-primary); color: white">
                            Confirmar anonimización
                        </button>
                        @if($documento->expediente_id)
                            <p class="text-[10px] mt-1.5 text-center" style="color: var(--color-muted)">
                                Al confirmar se actualizará el resumen del expediente
                            </p>
                        @endif
                    </div>
                @elseif($documento->isConfirmado())
                    <div class="mt-3 pt-3 flex items-center gap-2" style="border-top: 1px solid var(--color-border)">
                        <svg class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-[11px] text-emerald-700 font-medium">Anonimización confirmada</p>
                    </div>
                @endif
            </div>

            {{-- Detalles --}}
            <div class="bg-white rounded-lg p-4" style="border: 1px solid var(--color-border)">
                <h3 class="text-xs font-semibold uppercase tracking-wide mb-3" style="color: var(--color-muted)">Detalles</h3>
                <dl class="space-y-2.5">
                    <div>
                        <dt class="text-[10px] font-semibold uppercase tracking-wide" style="color: var(--color-subtle)">Formato</dt>
                        <dd class="text-xs mt-0.5" style="color: var(--color-text)">{{ strtoupper($documento->formato) }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-semibold uppercase tracking-wide" style="color: var(--color-subtle)">Tipo de documento</dt>
                        <dd class="text-xs mt-0.5 capitalize" style="color: var(--color-text)">{{ $documento->tipo_documento }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-semibold uppercase tracking-wide" style="color: var(--color-subtle)">Subido por</dt>
                        <dd class="text-xs mt-0.5" style="color: var(--color-text)">{{ $documento->usuario->nombre ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[10px] font-semibold uppercase tracking-wide" style="color: var(--color-subtle)">Fecha de carga</dt>
                        <dd class="text-xs mt-0.5" style="color: var(--color-text)">{{ $documento->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    @if($documento->expediente)
                        <div>
                            <dt class="text-[10px] font-semibold uppercase tracking-wide" style="color: var(--color-subtle)">Expediente</dt>
                            <dd class="text-xs mt-0.5">
                                <a href="{{ route('expedientes.show', $documento->expediente) }}" wire:navigate
                                   class="font-mono hover:underline" style="color: var(--color-primary)">
                                    {{ $documento->expediente->numero_expediente ?? 'Sin número' }}
                                </a>
                            </dd>
                        </div>
                    @endif
                </dl>
            </div>

        </div>

        {{-- RIGHT — Texto --}}
        <div class="flex-1 min-w-0">
            <div class="bg-white rounded-lg" style="border: 1px solid var(--color-border)">

                @if($documento->isRevisable())
                    <div x-data="{ tab: 'anon' }">
                        <div class="flex items-center px-5 py-0" style="border-bottom: 1px solid var(--color-border)">
                            <button @click="tab = 'anon'"
                                    class="px-4 py-3 text-xs font-medium transition-colors"
                                    :class="tab === 'anon' ? 'border-b-2 border-current' : 'opacity-50'"
                                    style="color: var(--color-primary)">
                                Texto anonimizado
                            </button>
                            <button @click="tab = 'original'"
                                    class="px-4 py-3 text-xs font-medium transition-colors"
                                    :class="tab === 'original' ? 'border-b-2 border-current' : 'opacity-50'"
                                    style="color: var(--color-primary)">
                                Texto original (resaltado)
                            </button>
                        </div>

                        <div class="px-5 py-4">
                            <div x-show="tab === 'anon'">
                                <pre class="text-xs leading-relaxed whitespace-pre-wrap font-sans"
                                     style="color: var(--color-text)">{{ $documento->texto_anonimizado }}</pre>
                            </div>

                            <div x-show="tab === 'original'" x-cloak>
                                <p class="text-xs leading-relaxed font-sans" style="color: var(--color-text)">
                                    {!! $this->textoResaltado !!}
                                </p>
                                @if(!empty($documento->entidades))
                                    <div class="flex flex-wrap gap-2 mt-4 pt-3" style="border-top: 1px solid var(--color-border)">
                                        @foreach(['PER' => 'Persona', 'CI' => 'C. Identidad', 'NIT' => 'NIT', 'TEL' => 'Teléfono', 'DIR' => 'Dirección'] as $tipo => $label)
                                            @if(collect($documento->entidades)->where('tipo', $tipo)->count())
                                                <span class="inline-flex items-center gap-1 text-[10px] px-2 py-0.5 rounded-full"
                                                      style="background-color: {{ $colores[$tipo] }}; color: var(--color-text)">
                                                    {{ $label }}
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                @elseif($documento->isPendiente() || $documento->estado_extraccion === 'procesado')
                    <div class="flex items-center justify-between px-5 py-3.5" style="border-bottom: 1px solid var(--color-border)">
                        <h3 class="text-xs font-semibold uppercase tracking-wide" style="color: var(--color-muted)">Texto extraído</h3>
                    </div>
                    <div class="px-5 py-16 text-center">
                        <svg class="w-10 h-10 mx-auto mb-3 text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs font-medium text-amber-700 mb-1">Procesando documento...</p>
                        <p class="text-[11px]" style="color: var(--color-muted)">
                            El servicio FastAPI extrae el texto y detecta entidades sensibles automáticamente.
                        </p>
                    </div>

                @else
                    <div class="flex items-center justify-between px-5 py-3.5" style="border-bottom: 1px solid var(--color-border)">
                        <h3 class="text-xs font-semibold uppercase tracking-wide" style="color: var(--color-muted)">Texto extraído</h3>
                    </div>
                    <div class="px-5 py-16 text-center">
                        <svg class="w-10 h-10 mx-auto mb-3 text-red-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs font-medium text-red-700 mb-1">Error al procesar el documento</p>
                        <p class="text-[11px]" style="color: var(--color-muted)">No se pudo extraer el texto de este archivo.</p>
                    </div>
                @endif

            </div>
        </div>

    </div>

</div>
