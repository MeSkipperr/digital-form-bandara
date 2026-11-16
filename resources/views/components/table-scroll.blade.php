{{--
    Horizontal scroll box for a wide table on mobile and tablet. Give the table a min-w-* class so it keeps its full
    column layout and scrolls sideways instead of squeezing; from xl up it renders exactly like a plain table.
    app.js opens [data-dropdown] menus inside it as fixed panels, so the scroll box does not clip them.
--}}
<div {{ $attributes->class(['overflow-x-auto overscroll-x-contain rounded-xl xl:overflow-visible']) }} data-gulir-tabel>
    {{ $slot }}
</div>
