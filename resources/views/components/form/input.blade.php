@props(['label' => null, 'mono' => false])

<label class="block">
    @if ($label)
        <x-form.label>{{ $label }}</x-form.label>
    @endif
    <input {{ $attributes->class(['h-10 w-full rounded-lg border border-line bg-canvas px-3.5 text-sm text-ink outline-none placeholder:text-muted focus:border-primary', 'font-mono' => $mono])->merge(['type' => 'text']) }}>
</label>
