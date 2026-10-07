{{-- Strategy <option>s for a touchpoint form — see StrategyPlayService::optionsFor() --}}
@php
    $label = fn ($s) => $s->title
        . (($types = array_keys($s->prospectAngles())) ? ' · ' . collect($types)->map(fn ($t) => \App\Models\Strategy::TEMPERATURES[$t])->implode('/') : '')
        . ($s->uses_count ? " · used {$s->uses_count}×" : ' · never used');
@endphp

@if ($options['suggested']->count())
    <optgroup label="Suggested for this {{ $personLabel }}">
        @foreach ($options['suggested'] as $s)
            <option value="{{ $s->id }}">{{ $label($s) }}</option>
        @endforeach
    </optgroup>
@endif

@if ($options['other']->count())
    <optgroup label="{{ $options['suggested']->count() ? 'Other strategies' : 'Strategies' }}">
        @foreach ($options['other'] as $s)
            <option value="{{ $s->id }}">{{ $label($s) }}</option>
        @endforeach
    </optgroup>
@endif
