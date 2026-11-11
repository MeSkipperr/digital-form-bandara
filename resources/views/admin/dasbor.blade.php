<x-layouts.admin title="Dasbor" active="dasbor">
    <div class="grid grid-cols-[372fr_748fr] gap-x-4 gap-y-6">
        <x-card class="flex flex-col px-6 pt-6 pb-8">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold">Perbandingan Status Alat</h2>
                <a href="{{ route('admin.alat.index') }}" class="text-xs text-primary hover:underline">Kelola Alat ›</a>
            </div>
            <div class="mt-auto flex items-center gap-5">
                <x-donut-chart :segments="$statusAlat" caption="Total alat" />
                <x-chart-legend :segments="$statusAlat" class="flex-1" />
            </div>
        </x-card>

        <x-card class="px-6 pt-5 pb-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold">Jumlah Form yang Diisi Setiap Hari</h2>
                <div class="flex items-center gap-2 text-xs">
                    @foreach (['7' => '7 hari', '30' => '30 hari'] as $value => $label)
                        <a
                            href="{{ request()->fullUrlWithQuery(['periode' => $value]) }}"
                            @class([
                                'flex h-7 items-center rounded-md px-3',
                                'bg-hover font-bold text-primary' => $periode === $value,
                                'text-muted hover:text-body' => $periode !== $value,
                            ])
                        >{{ $label }}</a>
                    @endforeach
                </div>
            </div>
            <x-line-chart :points="$formPerHari" :highlight="$titikSorot" :max="$batasGrafik" :step="$langkahGrafik" class="mt-1" />
        </x-card>

        <x-card class="flex flex-col px-6 pt-6 pb-6">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold">Form Hari Ini</h2>
                <a href="{{ route('admin.riwayat.index') }}" class="text-xs text-primary hover:underline">Riwayat ›</a>
            </div>
            <x-donut-chart :segments="$formHariIni" caption="Form" :size="174" class="mx-auto mt-6" />
            <x-chart-legend :segments="$formHariIni" class="mt-auto" />
        </x-card>

        <x-card class="px-6 pt-6 pb-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold">Pengguna Aktif</h2>
                <a href="{{ route('admin.pengguna.index') }}" class="text-xs text-primary hover:underline">Kelola Pengguna ›</a>
            </div>
            <ul class="mt-4 divide-y divide-line-soft">
                @forelse ($penggunaAktif as $pengguna)
                    <li class="flex items-center gap-3 py-2.5">
                        <x-avatar :initials="$pengguna->inisial()" :color="$pengguna->warnaAvatar()" class="size-10 text-sm" />
                        <div class="flex flex-1 flex-col">
                            <span class="text-sm text-ink">{{ $pengguna->nama }}</span>
                            <span class="text-xs text-muted">{{ '@'.$pengguna->id }}</span>
                        </div>
                        <span class="flex items-center gap-4 text-xs text-success">
                            <span class="size-2 rounded-full bg-success"></span>
                            Daring
                        </span>
                    </li>
                @empty
                    <li class="py-6 text-center text-sm text-muted">Tidak ada pengguna yang sedang daring.</li>
                @endforelse
            </ul>
        </x-card>
    </div>
</x-layouts.admin>
