{{-- Outlined "back" link. The label defaults to "Kembali" and can be replaced through the slot. --}}
@props(['href'])

<a href="{{ $href }}" {{ $attributes->class(['inline-flex h-9 items-center gap-2 rounded-lg border border-line bg-white px-4 text-[13px] text-ink hover:bg-hover']) }}>
    <x-icon name="chevron-kiri" class="size-4" />
    {{ $slot->isEmpty() ? 'Kembali' : $slot }}
</a>
