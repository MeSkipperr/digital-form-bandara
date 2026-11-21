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
    <body class="bg-canvas font-sans text-ink antialiased data-sidebar-terbuka:overflow-hidden">
        <div class="flex min-h-screen">
            {{-- Below xl the sidebar is an off-canvas drawer opened by the navbar's burger button (app.js [data-sidebar]). --}}
            <div
                class="invisible fixed inset-0 z-30 bg-overlay/45 opacity-0 transition-[opacity,visibility] duration-300 data-terbuka:visible data-terbuka:opacity-100 data-terbuka:transition-opacity xl:hidden"
                data-sidebar-latar
                aria-hidden="true"
            ></div>

            <aside
                id="sidebar"
                class="invisible fixed top-0 left-0 z-40 flex h-dvh w-4/5 max-w-xs shrink-0 -translate-x-full flex-col overflow-y-auto bg-sidebar px-3 pt-5 pb-6 shadow-xl transition-[translate,visibility] duration-300 ease-out data-terbuka:visible data-terbuka:translate-x-0 data-terbuka:transition-[translate] xl:visible xl:sticky xl:left-auto xl:z-auto xl:h-screen xl:w-60 xl:max-w-none xl:translate-x-0 xl:overflow-visible xl:shadow-none xl:transition-none"
                data-sidebar
            >


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

            <div class="flex min-w-0 flex-1 flex-col" data-konten-utama>
                <header class="sticky top-0 z-20 flex h-16 shrink-0 items-center gap-3 border-b border-line bg-white px-4 sm:h-20 sm:px-6 xl:static xl:z-auto xl:px-8">
                    <button
                        type="button"
                        class="-ml-2 flex size-10 shrink-0 items-center justify-center rounded-lg text-primary-dark transition-colors hover:bg-hover xl:hidden"
                        aria-label="Buka menu"
                        aria-controls="sidebar"
                        aria-expanded="false"
                        data-sidebar-buka
                    >
                        <x-icon name="menu" class="size-6" />
                    </button>
                    <div class="flex min-w-0 flex-1 flex-col justify-center gap-0.5">
                        <p class="truncate text-xs text-muted xl:overflow-visible xl:whitespace-normal">{{ $breadcrumb ?? 'Inspeksi Alat / '.$title }}</p>
                        <h1 class="truncate text-xl leading-tight font-bold text-primary-dark sm:text-2xl xl:overflow-visible xl:leading-[29px] xl:whitespace-normal">{{ $title }}</h1>
                    </div>
                </header>

                <main class="flex-1 px-4 pt-4 pb-6 sm:px-6 sm:pt-6 sm:pb-8 xl:px-8">
                    {{ $slot }}
                </main>
            </div>
        </div>

        {{ $modals ?? '' }}

        <x-image-zoom />
    </body>
</html>
