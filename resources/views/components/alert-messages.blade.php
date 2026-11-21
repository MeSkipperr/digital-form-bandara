{{--
    Flash status (green) and the first validation error (red) after a modal form is submitted.
    `formDenganGalatSendiri` lists `_form` keys (wildcards allowed) whose form already shows its errors under the
    inputs, so the red alert is not repeated for them.

    @var list<string> $formDenganGalatSendiri
--}}
@props(['formDenganGalatSendiri' => []])

@if (session('status'))
    <p {{ $attributes->class(['rounded-lg border border-success/30 bg-success/10 px-4 py-2.5 text-sm text-success']) }} role="status">{{ session('status') }}</p>
@endif

@if ($errors->any() && ! \Illuminate\Support\Str::is($formDenganGalatSendiri, (string) old('_form')))
    <p {{ $attributes->class(['rounded-lg border border-danger/30 bg-danger/10 px-4 py-2.5 text-sm text-danger']) }} role="alert">{{ $errors->first() }}</p>
@endif
