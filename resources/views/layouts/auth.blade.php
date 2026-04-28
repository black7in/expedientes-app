<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    @vite(['resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-slate-50 flex">

    {{-- Panel izquierdo decorativo --}}
    <div class="hidden lg:flex lg:w-1/2 flex-col justify-between p-12" style="background-color: var(--color-sidebar)">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-md flex items-center justify-center" style="background-color: var(--color-primary)">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                </svg>
            </div>
            <span class="text-white font-semibold tracking-tight">Rosales y Asociados</span>
        </div>

        <div>
            <blockquote class="text-slate-300 text-lg font-light leading-relaxed mb-6">
                "La justicia sin fuerza es impotente; la fuerza sin justicia es tiránica."
            </blockquote>
            <p class="text-slate-500 text-sm">— Blaise Pascal</p>
        </div>

        <p class="text-slate-600 text-xs">
            Sistema de Gestión de Expedientes Jurídicos · Cochabamba, Bolivia
        </p>
    </div>

    {{-- Panel derecho: formulario --}}
    <div class="flex-1 flex items-center justify-center p-8">
        <div class="w-full max-w-sm">
            {{-- Logo móvil --}}
            <div class="flex items-center gap-2 mb-8 lg:hidden">
                <div class="w-7 h-7 rounded-md flex items-center justify-center" style="background-color: var(--color-primary)">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                    </svg>
                </div>
                <span class="font-semibold text-slate-900 text-sm">Rosales y Asociados</span>
            </div>

            {{ $slot }}
        </div>
    </div>

    @livewireScripts
</body>
</html>
