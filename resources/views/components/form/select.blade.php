{{-- Native select styled as the design's dropdown. Options are value => label. --}}
@props(['label' => null, 'options' => [], 'selected' => null, 'surface' => 'bg-canvas'])

<label class="block">
    @if ($label)
        <x-form.label>{{ $label }}</x-form.label>
    @endif
    <span class="relative block">
        <select {{ $attributes->class(['h-10 w-full appearance-none rounded-lg border border-line pr-10 pl-3.5 text-sm text-ink outline-none focus:border-primary', $surface]) }}>
            @foreach ($options as $value => $text)
                <option value="{{ $value }}" @selected((string) $value === (string) $selected)>{{ $text }}</option>
            @endforeach
        </select>
        <x-icon name="chevron-bawah" class="pointer-events-none absolute top-1/2 right-3.5 size-4.5 -translate-y-1/2 text-muted" />
    </span>
</label>
