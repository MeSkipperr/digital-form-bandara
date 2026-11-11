@props(['label' => null])

<label class="block">
    @if ($label)
        <x-form.label>{{ $label }}</x-form.label>
    @endif
    <textarea {{ $attributes->class(['h-23 w-full resize-none rounded-lg border border-line bg-canvas px-3.5 py-2.5 text-sm text-ink outline-none placeholder:text-muted focus:border-primary']) }}>{{ $slot }}</textarea>
</label>
