{{-- Red warning box with a triangle icon, used inside delete confirmation modals. --}}
<div {{ $attributes->class(['flex items-start gap-3 rounded-lg border border-danger/25 bg-danger/5 px-4 py-3 text-[13px] leading-5 text-danger-dark']) }} role="alert">
    <x-icon name="peringatan" class="mt-0.5 size-4.5 text-danger" />
    <p>{{ $slot }}</p>
</div>
