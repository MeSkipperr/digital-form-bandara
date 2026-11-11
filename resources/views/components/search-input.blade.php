@props(['placeholder', 'name' => 'cari'])

<label {{ $attributes->class(['flex h-10 items-center gap-2.5 rounded-lg border border-line bg-white px-3.5 focus-within:border-primary']) }}>
    <x-icon name="cari" class="size-4 text-muted" />
    <input type="search" name="{{ $name }}" value="{{ request()->query($name) }}" placeholder="{{ $placeholder }}" class="w-full bg-transparent text-sm text-ink outline-none placeholder:text-muted">
</label>
