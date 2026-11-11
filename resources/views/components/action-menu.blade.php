{{-- Kebab (⋮) row menu. Put <x-action-menu.item> children in the slot. --}}
@props(['label' => 'Aksi'])

<details data-dropdown {{ $attributes->class(['relative inline-block']) }}>
    <summary class="flex size-8 cursor-pointer items-center justify-center rounded-md text-muted hover:bg-hover" aria-label="{{ $label }}">
        <x-icon name="menu-aksi" class="size-5" />
    </summary>
    <div class="absolute top-full right-0 z-20 mt-1.5 flex w-38 flex-col gap-0.5 rounded-lg border border-line bg-canvas p-1.5 shadow-lg">
        {{ $slot }}
    </div>
</details>
