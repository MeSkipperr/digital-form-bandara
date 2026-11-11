@php
    // Sample data from the Figma design. Replace with real queries when the backend is wired up.
    $statusAlat = [
        ['label' => 'Perlu Perbaikan', 'value' => 8, 'color' => '#D97706'],
        ['label' => 'Bagus', 'value' => 18, 'color' => '#16A34A'],
    ];

    $formHariIni = [
        ['label' => 'Menunggu', 'value' => 6, 'color' => '#64748B'],
        ['label' => 'Baik', 'value' => 14, 'color' => '#16A34A'],
        ['label' => 'Buruk', 'value' => 3, 'color' => '#DC2626'],
    ];

    $formPerHari = [
        ['label' => 'Sen', 'value' => 14, 'tooltip' => 'Senin, 26 Okt'],
        ['label' => 'Sel', 'value' => 21, 'tooltip' => 'Selasa, 27 Okt'],
        ['label' => 'Rab', 'value' => 19, 'tooltip' => 'Rabu, 28 Okt'],
        ['label' => 'Kam', 'value' => 27, 'tooltip' => 'Kamis, 29 Okt'],
        ['label' => 'Jum', 'value' => 23, 'tooltip' => 'Jumat, 30 Okt'],
        ['label' => 'Sab', 'value' => 9, 'tooltip' => 'Sabtu, 31 Okt'],
        ['label' => 'Min', 'value' => 6, 'tooltip' => 'Minggu, 1 Nov'],
    ];

    $penggunaAktif = [
        ['inisial' => 'YP', 'nama' => 'Yoga Pratama', 'username' => 'yoga001', 'warna' => '#12345A'],
        ['inisial' => 'MD', 'nama' => 'Made Dwi Astuti', 'username' => 'made002', 'warna' => '#1D4E89'],
        ['inisial' => 'KA', 'nama' => 'Komang Ayu Lestari', 'username' => 'komang003', 'warna' => '#19A7A0'],
        ['inisial' => 'KS', 'nama' => 'Kadek Surya Dharma', 'username' => 'kadek005', 'warna' => '#475569'],
        ['inisial' => 'WS', 'nama' => 'Wayan Sari Dewi', 'username' => 'wayan006', 'warna' => '#0F766E'],
    ];

    $periode = request()->query('periode') === '30' ? '30' : '7';
@endphp

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
            <x-line-chart :points="$formPerHari" :highlight="3" class="mt-1" />
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
                @foreach ($penggunaAktif as $pengguna)
                    <li class="flex items-center gap-3 py-2.5">
                        <x-avatar :initials="$pengguna['inisial']" :color="$pengguna['warna']" class="size-10 text-sm" />
                        <div class="flex flex-1 flex-col">
                            <span class="text-sm text-ink">{{ $pengguna['nama'] }}</span>
                            <span class="text-xs text-muted">{{ '@'.$pengguna['username'] }}</span>
                        </div>
                        <span class="flex items-center gap-4 text-xs text-success">
                            <span class="size-2 rounded-full bg-success"></span>
                            Daring
                        </span>
                    </li>
                @endforeach
            </ul>
        </x-card>
    </div>
</x-layouts.admin>
