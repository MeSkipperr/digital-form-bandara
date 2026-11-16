{{--
    "Menampilkan x dari y" summary with numbered page links for a LengthAwarePaginator.
    Shows the first and last page, the pages next to the current one, and "…" for the gaps.
--}}
@props([
    'paginator',
    'label',
])

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator $paginator */
    $currentPage = $paginator->currentPage();
    $lastPage = $paginator->lastPage();

    $pages = collect([1, $currentPage - 1, $currentPage, $currentPage + 1, $lastPage])
        ->filter(fn (int $page): bool => $page >= 1 && $page <= $lastPage)
        ->unique()
        ->sort()
        ->values();

    $linkClass = 'flex h-7 min-w-9 items-center justify-center rounded-md border border-line px-2';
@endphp

<div {{ $attributes->class(['flex flex-col items-center gap-3 sm:flex-row sm:justify-between sm:gap-4']) }}>
    <p class="text-xs text-muted">Menampilkan {{ $paginator->count() }} dari {{ $paginator->total() }} {{ $label }}</p>

    @if ($paginator->hasPages())
        <nav class="flex flex-wrap items-center justify-center gap-2 text-sm" aria-label="Halaman">
            @if ($paginator->onFirstPage())
                <span class="{{ $linkClass }} bg-white text-line" aria-hidden="true"><x-icon name="chevron-kiri" class="size-4" /></span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="{{ $linkClass }} bg-white text-muted hover:bg-hover" rel="prev" aria-label="Halaman sebelumnya">
                    <x-icon name="chevron-kiri" class="size-4" />
                </a>
            @endif

            @foreach ($pages as $position => $page)
                @if ($position > 0 && $page - $pages[$position - 1] > 1)
                    <span class="px-1 text-muted" aria-hidden="true">…</span>
                @endif

                <a
                    href="{{ $paginator->url($page) }}"
                    @class([
                        $linkClass,
                        'bg-line text-primary' => $page === $currentPage,
                        'bg-white text-muted hover:bg-hover' => $page !== $currentPage,
                    ])
                    @if ($page === $currentPage) aria-current="page" @endif
                >{{ $page }}</a>
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="{{ $linkClass }} bg-white text-muted hover:bg-hover" rel="next" aria-label="Halaman berikutnya">
                    <x-icon name="chevron-kanan" class="size-4" />
                </a>
            @else
                <span class="{{ $linkClass }} bg-white text-line" aria-hidden="true"><x-icon name="chevron-kanan" class="size-4" /></span>
            @endif
        </nav>
    @endif
</div>
