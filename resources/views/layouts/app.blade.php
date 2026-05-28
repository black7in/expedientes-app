<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($title) ? $title . ' — ' . config('app.name') : config('app.name') }}</title>
    @vite(['resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen flex text-sm" style="background-color: var(--color-page-bg); color: var(--color-text)">

    @persist('sidebar')
    <aside class="w-56 min-h-screen flex flex-col fixed left-0 top-0 z-30"
           style="background-color: var(--color-sidebar)"
           x-data="{ path: window.location.pathname }"
           x-on:livewire:navigated.window="path = window.location.pathname">

        {{-- Marca --}}
        <div class="px-4 py-4 flex items-center gap-2.5" style="border-bottom: 1px solid var(--color-sidebar-border)">
            <div class="w-7 h-7 rounded-md flex items-center justify-center flex-shrink-0"
                 style="background-color: var(--color-primary)">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                </svg>
            </div>
            <div class="leading-tight min-w-0">
                <p class="text-white font-semibold text-xs tracking-tight truncate">Rosales y Asociados</p>
                <p class="text-xs" style="color: var(--color-sidebar-text)">Expedientes</p>
            </div>
        </div>

        {{-- Navegación --}}
        <nav class="flex-1 px-2 py-3 space-y-0.5">
            <x-nav-item route="dashboard" icon="home" path-prefix="/dashboard">Dashboard</x-nav-item>
            <x-nav-item route="expedientes.index" icon="document" path-prefix="/expedientes">Expedientes</x-nav-item>
            <x-nav-item route="documentos.index" icon="upload" path-prefix="/documentos">Documentos</x-nav-item>
            <x-nav-item route="personas.index" icon="person" path-prefix="/personas">Partes procesales</x-nav-item>
            <x-nav-item route="generacion.crear" icon="spark" path-prefix="/generacion">Generar documento</x-nav-item>
            <x-nav-item route="jurisprudencia.consulta" icon="search" path-prefix="/jurisprudencia">Jurisprudencia TSJ</x-nav-item>

            @if(auth()->user()->isAdmin())
                <div class="pt-4 pb-1 px-2">
                    <span class="text-[10px] font-semibold uppercase tracking-widest"
                          style="color: var(--color-sidebar-border)">Admin</span>
                </div>
                <x-nav-item route="usuarios.index" icon="users" path-prefix="/usuarios">Usuarios</x-nav-item>
                <x-nav-item route="config.juzgados" icon="building" path-prefix="/configuracion/juzgados">Juzgados</x-nav-item>
                <x-nav-item route="config.tipos-proceso" icon="settings" path-prefix="/configuracion/tipos">Configuración</x-nav-item>
                <x-nav-item route="config.base-conocimiento" icon="spark" path-prefix="/configuracion/base">Base de conocimiento</x-nav-item>
            @endif
        </nav>

        {{-- Usuario --}}
        <div class="px-2 py-3" style="border-top: 1px solid var(--color-sidebar-border)">
            <div class="flex items-center gap-2.5 px-2 py-2 rounded-md group cursor-default"
                 x-data
                 x-on:mouseenter="$el.style.backgroundColor = 'var(--color-sidebar-active)'"
                 x-on:mouseleave="$el.style.backgroundColor = 'transparent'">
                <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0 text-[11px] font-bold text-white"
                     style="background-color: var(--color-primary)">
                    {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium truncate" style="color: var(--color-sidebar-text-hover)">
                        {{ auth()->user()->nombre }}
                    </p>
                    <p class="text-[11px] capitalize" style="color: var(--color-sidebar-text)">
                        {{ auth()->user()->rol }}
                    </p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Cerrar sesión"
                            class="opacity-0 group-hover:opacity-100 transition-opacity p-1"
                            style="color: var(--color-sidebar-text)">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>
    @endpersist

    {{-- Contenido --}}
    <div class="flex-1 ml-56 flex flex-col min-h-screen">
        <header class="sticky top-0 z-20 bg-white h-12 flex items-center justify-between px-7"
                style="border-bottom: 1px solid var(--color-border)">
            <h1 class="font-semibold text-sm" style="color: var(--color-text)">{{ $title ?? 'Dashboard' }}</h1>
            <span class="text-xs" style="color: var(--color-subtle)">
                {{ now()->translatedFormat('d \d\e F, Y') }}
            </span>
        </header>

        <main class="flex-1 p-7">
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
