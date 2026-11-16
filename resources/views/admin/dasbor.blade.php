@php
    /** @var array<string, string> $daftarPeriode */
    $daftarPeriode = ['7' => '7 hari', '30' => '30 hari'];
@endphp

<x-layouts.admin title="Dasbor" active="dasbor">
    <div class="grid grid-cols-1 gap-4 xl:grid-cols-[372fr_748fr]">
        <x-card class="flex flex-col px-6 pt-6 pb-8">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-ink">Perbandingan Status Alat</h2>
                <a href="{{ route('admin.alat.index') }}" class="text-xs text-primary hover:underline">Alat ›</a>
            </div>
            <div class="mt-8 flex flex-1 flex-col items-center gap-6 sm:flex-row">
                <x-donut-chart id="grafik-status-alat" :segments="$statusAlat" caption="Total alat" />
                <x-chart-legend for="grafik-status-alat" :segments="$statusAlat" class="flex-1 self-stretch sm:self-auto" />
            </div>
        </x-card>

        <x-card class="px-6 pt-5 pb-4">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-base font-bold text-ink">Jumlah Form yang Diisi Setiap Hari</h2>
                <details data-dropdown class="relative shrink-0">
                    <summary class="flex h-7 cursor-pointer items-center rounded-md bg-hover px-3 text-xs font-bold whitespace-nowrap text-primary hover:bg-line">
                        {{ $daftarPeriode[$periode] }}
                    </summary>
                    <div class="absolute top-full right-0 z-20 mt-1.5 flex w-28 flex-col gap-0.5 rounded-lg border border-line bg-white p-1.5 shadow-lg">
                        @foreach ($daftarPeriode as $value => $label)
                            <a
                                href="{{ request()->fullUrlWithQuery(['periode' => $value]) }}"
                                @class([
                                    'flex h-8 items-center rounded-md px-3 text-xs hover:bg-hover',
                                    'font-bold text-primary' => $periode === $value,
                                    'text-body' => $periode !== $value,
                                ])
                                @if ($periode === $value) aria-current="true" @endif
                            >{{ $label }}</a>
                        @endforeach
                    </div>
                </details>
            </div>
            <div class="overflow-x-auto overscroll-x-contain sm:overflow-visible">
                <x-line-chart :points="$formPerHari" :highlight="$titikSorot" :max="$batasGrafik" :step="$langkahGrafik" class="mt-1 min-w-xl max-sm:touch-pan-x sm:min-w-0" />
            </div>
        </x-card>

        <x-card class="flex flex-col px-6 pt-6 pb-6">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-ink">Form Hari Ini</h2>
                <a href="{{ route('admin.riwayat.index') }}" class="text-xs text-primary hover:underline">Riwayat ›</a>
            </div>
            <x-donut-chart id="grafik-form-hari-ini" :segments="$formHariIni" caption="Form" :size="180" class="mx-auto mt-6" />
            <x-chart-legend for="grafik-form-hari-ini" :segments="$formHariIni" class="mt-8" />
        </x-card>

        <x-card class="px-6 pt-6 pb-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-ink">Pengguna Aktif</h2>
                <a href="{{ route('admin.pengguna.index') }}" class="text-xs text-primary hover:underline">Pengguna ›</a>
            </div>
            <ul class="mt-4 divide-y divide-line-soft">
                @forelse ($penggunaAktif as $pengguna)
                    <li class="flex items-center gap-3 py-2.5">
                        <x-avatar :initials="$pengguna->inisial()" :color="$pengguna->warnaAvatar()" class="size-10 text-sm" />
                        <div class="flex min-w-0 flex-1 flex-col">
                            <span class="truncate text-sm text-ink">{{ $pengguna->nama }}</span>
                            <span class="truncate text-xs text-muted">{{ '@'.$pengguna->id }}</span>
                        </div>
                        <span class="flex items-center gap-5 text-xs text-success">
                            <span class="size-2 rounded-full bg-success"></span>
                            Aktif
                        </span>
                    </li>
                @empty
                    <li class="py-6 text-center text-sm text-muted">Tidak ada pengguna yang sedang aktif.</li>
                @endforelse
            </ul>
        </x-card>
    </div>
</x-layouts.admin>
