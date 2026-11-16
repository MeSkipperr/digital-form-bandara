{{-- Native select styled as the design's dropdown. Options are value => label. Shows a red validation message under it. --}}
@props(['label' => null, 'options' => [], 'selected' => null, 'surface' => 'bg-canvas', 'error' => null])

<label class="block">
    @if ($label)
        <x-form.label>{{ $label }}</x-form.label>
    @endif
    <span class="relative block">
        <select
            {{ $attributes->class([
                'h-10 w-full appearance-none rounded-lg border pr-10 pl-3.5 text-sm text-ink outline-none',
                'border-line focus:border-primary' => ! $error,
                'border-danger' => $error,
                $surface,
            ]) }}
            @if ($error) aria-invalid="true" @endif
        >
            @foreach ($options as $value => $text)
                <option value="{{ $value }}" @selected((string) $value === (string) $selected)>{{ $text }}</option>
            @endforeach
        </select>
        <x-icon name="chevron-bawah" class="pointer-events-none absolute top-1/2 right-3.5 size-4.5 -translate-y-1/2 text-muted" />
    </span>
    @if ($error)
        <span class="mt-1.5 block text-xs text-danger">{{ $error }}</span>
    @endif
</label>
