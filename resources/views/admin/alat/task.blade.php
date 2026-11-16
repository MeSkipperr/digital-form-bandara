{{--
    Tasks of one form assigned to the alat. Tasks belong to the template form and are shared by every alat using it,
    so they are added and changed on the form's Kelola Tugas page.

    @var \App\Models\Alat $alat
    @var \App\Models\TemplateForm $templateForm
    @var \Illuminate\Database\Eloquent\Collection<int, \App\Models\Tugas> $daftarTugas
--}}
<x-layouts.admin title="Kelola Task" active="alat" breadcrumb="Inspeksi Alat / Alat / Form / Task">
    <x-back-button :href="route('admin.alat.form.index', $alat)" />

    <div class="mt-7 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-ink sm:text-2xl">{{ $templateForm->nama }}</h2>
            <p class="mt-1 text-[13px] text-muted">{{ $alat->id }}, {{ $daftarTugas->count() }} task</p>
        </div>
        <x-button :href="route('admin.form.tugas.index', $templateForm)" class="w-full sm:w-40">
            <x-icon name="pensil" class="size-4" />
            Kelola Task
        </x-button>
    </div>

    <x-card class="mt-8">
        <ul class="divide-y divide-line-soft">
            @forelse ($daftarTugas as $tugas)
                <li class="flex h-15 items-center gap-4 px-4 sm:px-6">
                    <span class="size-5 shrink-0 rounded-md border border-line bg-canvas" aria-hidden="true"></span>
                    <span class="min-w-0 flex-1 truncate text-sm text-ink">{{ $tugas->nama }}</span>
                </li>
            @empty
                <li class="px-6 py-10 text-center text-sm text-muted">Belum ada task di form ini.</li>
            @endforelse
        </ul>
    </x-card>

    <p class="mt-3 text-xs text-muted">Task dipakai bersama oleh semua alat yang memakai form ini.</p>
</x-layouts.admin>
