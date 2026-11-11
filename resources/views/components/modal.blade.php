{{--
    Centered modal (480px, radius 16) built on the native <dialog> element.
    Open it from any element with data-modal-open="{{ id }}".
--}}
@props([
    'id',
    'title' => null,
    'description' => null,
    'width' => 'w-120',
])

<dialog id="{{ $id }}" {{ $attributes->class(['m-auto max-w-[calc(100vw-2rem)] rounded-2xl bg-white p-6 text-ink shadow-xl', $width]) }} aria-labelledby="{{ $id }}-title">
    @if ($title)
        <div class="mb-6 flex items-start justify-between gap-4">
            <div class="flex flex-col gap-2">
                <h2 id="{{ $id }}-title" class="text-xl leading-6 font-bold">{{ $title }}</h2>
                @if ($description)
                    <p class="text-xs text-muted">{{ $description }}</p>
                @endif
            </div>
            <button type="button" class="text-body hover:text-ink" data-modal-close aria-label="Tutup">
                <x-icon name="tutup" class="size-5" />
            </button>
        </div>
    @endif

    {{ $slot }}

    @isset($footer)
        <div {{ $footer->attributes->class(['mt-8 flex items-center justify-end gap-3']) }}>
            {{ $footer }}
        </div>
    @endisset
</dialog>
