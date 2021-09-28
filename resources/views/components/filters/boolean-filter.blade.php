@props([
    'theme' => '',
    'inline' => null,
    'booleanFilter' => null
])
<div>
    @if(filled($booleanFilter))
        <div wire:ignore class="@if($inline) {{ $theme->divClassInline }} @endif {{ $theme->divClassNotInline }}{!! ($booleanFilter['class'] != '') ?? '' !!}">
            @if(!$inline)
                <label for="input_boolean_filter_{!! $booleanFilter['field']!!}">{{ $booleanFilter['label'] }}</label>
            @endif
            <div class="relative">
                <select id="input_boolean_filter_{!! $booleanFilter['field']!!}" class="power_grid {{ $theme->inputClass }} {{ (isset($class)) ? $class : 'w-9/12' }}"
                        wire:input.debounce.300ms="filterBoolean('{{ $booleanFilter['dataField'] }}', $event.target.value, '{{ $booleanFilter['label'] }}')">
                    <option value="all">{{ trans('livewire-powergrid::datatable.boolean_filter.all') }}</option>
                    <option value="true">{{ $booleanFilter['true_label']}}</option>
                    <option value="false">{{ $booleanFilter['false_label']}}</option>
                </select>
                <div class="{{ $theme->relativeDivClass }}">
                    <x-livewire-powergrid::icons.down class="w-5 h-5"/>
                </div>
            </div>
        </div>
    @endif
</div>
