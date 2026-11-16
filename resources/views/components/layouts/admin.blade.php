@props([
    'title',
    'active',
    'breadcrumb' => null,
])

@php
    /**
     * Sidebar menu from the "01-dashboard" design.
     *
     * @var array<string, array{label: string, icon: string, route: string}> $menu
     */
    $menu = [
        'dasbor' => ['label' => 'Dasbor', 'icon' => 'dasbor', 'route' => 'admin.dasbor'],
        'alat' => ['label' => 'Alat', 'icon' => 'alat', 'route' => 'admin.alat.index'],
        'form' => ['label' => 'Form', 'icon' => 'form', 'route' => 'admin.form.index'],
        'pengguna' => ['label' => 'Pengguna', 'icon' => 'pengguna', 'route' => 'admin.pengguna.index'],
        'riwayat' => ['label' => 'Riwayat', 'icon' => 'riwayat', 'route' => 'admin.riwayat.index'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title }} · {{ config('app.name', 'X-Ray Check') }}</title>

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-canvas font-sans text-ink antialiased">
        <div class="flex min-h-screen">
            <aside class="sticky top-0 flex h-screen w-60 shrink-0 flex-col bg-sidebar px-3 pt-5 pb-6">
                <a href="{{ route('admin.dasbor') }}" class="flex items-center gap-3 px-3">
                    <span class="flex size-9 items-center justify-center rounded-lg bg-teal">
                        <x-icon name="alat" class="size-5 text-white" />
                    </span>
                    <span class="flex flex-col">
                        <span class="text-[15px] font-bold leading-[18px] text-white">X-Ray Check</span>
                        <span class="text-[11px] leading-[13px] text-sidebar-muted">Inspeksi Alat</span>
                    </span>
                </a>

                <p class="mt-6.5 px-3 text-[11px] font-bold tracking-wide text-sidebar-label">MENU</p>

                <nav class="mt-2.5 flex flex-col gap-1.5" aria-label="Menu utama">
                    @foreach ($menu as $key => $item)
                        @php($isActive = $key === $active)
                        <a
                            href="{{ route($item['route']) }}"
                            @class([
                                'relative flex h-11 items-center gap-3.5 rounded-lg pl-6.5 text-sm transition-colors',
                                'bg-white/10 font-bold text-white before:absolute before:top-1/2 before:left-0 before:h-5.5 before:w-0.75 before:-translate-y-1/2 before:rounded-full before:bg-teal' => $isActive,
                                'text-sidebar-text hover:bg-white/5 hover:text-white' => ! $isActive,
                            ])
                            @if ($isActive) aria-current="page" @endif
                        >
                            <x-icon :name="$item['icon']" @class(['size-5', 'text-white' => $isActive, 'text-sidebar-icon' => ! $isActive]) />
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="mx-1 mt-auto flex items-center gap-3 border-t border-sidebar-line px-2 pt-5">
                    <x-avatar initials="AU" color="#19A7A0" class="size-9 text-[13px]" />
                    <div class="flex min-w-0 flex-1 flex-col">
                        <span class="truncate text-[13px] font-bold leading-4 text-white">Admin Utama</span>
                        <span class="truncate text-xs leading-[15px] text-sidebar-muted">{{ '@admin' }}</span>
                    </div>
                    <button
                        type="button"
                        class="flex size-8 shrink-0 items-center justify-center rounded-lg border border-sidebar-line text-sidebar-icon transition-colors hover:bg-white/10 hover:text-white"
                        aria-label="Keluar"
                        title="Keluar"
                    >
                        <x-icon name="keluar" class="size-4" />
                    </button>
                </div>
            </aside>

            <div class="flex min-w-0 flex-1 flex-col">
                <header class="flex h-20 shrink-0 flex-col justify-center gap-0.5 border-b border-line bg-white px-8">
                    <p class="text-xs text-muted">{{ $breadcrumb ?? 'Inspeksi Alat / '.$title }}</p>
                    <h1 class="text-2xl leading-[29px] font-bold text-primary-dark">{{ $title }}</h1>
                </header>

                <main class="flex-1 px-8 pt-6 pb-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        {{ $modals ?? '' }}
    </body>
</html>
