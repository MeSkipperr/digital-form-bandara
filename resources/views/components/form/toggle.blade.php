@props(['checked' => false])

<label class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer">
    <input type="checkbox" {{ $attributes->class(['peer sr-only']) }} @checked($checked)>
    <span class="absolute inset-0 rounded-full bg-line transition-colors peer-checked:bg-primary peer-focus-visible:ring-2 peer-focus-visible:ring-primary/40"></span>
    <span class="absolute top-0.75 left-0.75 size-4.5 rounded-full bg-white transition-transform peer-checked:translate-x-5"></span>
</label>
