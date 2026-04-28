@props(['estado'])

@php
    $clases = match($estado) {
        'activo'       => 'bg-green-50 text-green-700 ring-green-600/20',
        'suspendido'   => 'bg-amber-50 text-amber-700 ring-amber-600/20',
        'en_apelacion' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
        'archivado'    => 'bg-slate-100 text-slate-600 ring-slate-500/20',
        'concluido'    => 'bg-slate-100 text-slate-500 ring-slate-400/20',
        default        => 'bg-slate-100 text-slate-600 ring-slate-500/20',
    };

    $etiquetas = [
        'activo'       => 'Activo',
        'suspendido'   => 'Suspendido',
        'en_apelacion' => 'En apelación',
        'archivado'    => 'Archivado',
        'concluido'    => 'Concluido',
    ];
@endphp

<span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-medium ring-1 ring-inset {{ $clases }}">
    {{ $etiquetas[$estado] ?? $estado }}
</span>
