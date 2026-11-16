{{-- Text input with an optional label and a red validation message under it. --}}
@props(['label' => null, 'mono' => false, 'error' => null])

<label class="block">
    @if ($label)
        <x-form.label>{{ $label }}</x-form.label>
    @endif
    <input
        {{ $attributes->class([
            'h-10 w-full rounded-lg border bg-canvas px-3.5 text-sm text-ink outline-none placeholder:text-muted read-only:cursor-not-allowed read-only:text-muted',
            'border-line focus:border-primary' => ! $error,
            'border-danger' => $error,
            'font-mono' => $mono,
        ])->merge(['type' => 'text']) }}
        @if ($error) aria-invalid="true" @endif
    >
    @if ($error)
        <span class="mt-1.5 block text-xs text-danger">{{ $error }}</span>
    @endif
</label>
