@inject('helperClass','PowerComponents\LivewirePowerGrid\Helpers\Helpers')
@props([
    'actions' => null,
    'theme' => null,
    'row' => null,
    'tableName' => null,
])
<div>
    @if(isset($actions) && count($actions) && $row !== '')
        @foreach($actions as $key => $action)
            <td class="pg-actions {{ $theme->table->tdBodyClass }}"
                style="{{ $theme->table->tdBodyStyle }}">
                @php
                    $class            = filled($action->class) ? $action->class : $theme->actions->headerBtnClass;
                    $class            = $attributes->class($class);
                    if($action->singleParam) {
                        $actionParameters = $helperClass->makeActionParameter($action->param, $row);
                    } else {
                        $actionParameters = $helperClass->makeActionParameters($action->param, $row);
                    }

                    $rules                 = $helperClass->makeActionRules($action, $row);

                    $ruleRedirect          = data_get($rules, 'redirect');
                    $ruleDisabled          = data_get($rules, 'disable');
                    $ruleHide              = data_get($rules, 'hide', false);
                    $ruleSetAttribute      = data_get($rules, 'setAttribute');
                    $ruleEmit              = data_get($rules, 'emit');
                    $ruleEmitTo            = data_get($rules, 'emitTo');
                    $ruleCaption           = data_get($rules, 'caption');
                    $ruleSetBladeComponent = data_get($rules, 'bladeComponent');

                    $action->emit     = false;
                    $action->emitTo   = false;

                    if(isset($ruleSetAttribute['attribute'])) {
                         $class = $attributes->merge([$ruleSetAttribute['attribute'] => $ruleSetAttribute['value']])->class($class);
                    }

                    if (filled($ruleEmit)) {
                        $event['event']  = $ruleEmit['event'];
                        $event['params'] = $helperClass->makeActionParameters(data_get($ruleEmit, 'params', []), $row);
                        $action->emit = true;
                    } else if (filled($ruleEmitTo) ) {
                        $event['to']     = $ruleEmitTo['to'] ?? '';
                        $event['event']  = $ruleEmitTo['event'];
                        $event['params'] = $helperClass->makeActionParameters(data_get($ruleEmitTo, 'params', []), $row);
                        $action->emitTo = true;
                    } else {
                        if (filled($action->event)) {
                            $action->emit    = true;
                            $event['event']  = $action->event;
                            $event['params'] = $actionParameters;

                            if (filled($action->to)) {
                                $action->emit    = false;
                                $action->emitTo  = true;
                                $event['to']     = $action->to;
                            }
                        }
                     }

                    if (filled($action->bladeComponent)) {
                        if (filled($ruleSetBladeComponent)){
                            $ruleBladeComponent = $ruleSetBladeComponent['component'];
                            $attributesBag = $helperClass->makeAttributesBag($ruleSetBladeComponent['params']);

                        } else {
                            $attributesBag = $helperClass->makeAttributesBag($actionParameters);

                        }
                    }
                @endphp
                <div class="w-full md:w-auto"
                     style="display: {{ $ruleHide ? 'none': 'block' }}"
                >
                    @if((filled($action->event) || isset($event['event']) || filled($action->view || $action->toggleDetail))
                        && is_null($ruleRedirect) && !filled($action->route))
                        <button
                            @if($action->toggleDetail)
                            wire:click.prevent="toggleDetail({{ $row->{$primaryKey} }})"
                            @endif
                            @if($action->emit)
                            wire:click='$emit("{{ $event['event'] }}", @json($event['params']))'
                            @endif
                            @if($action->emitTo)
                            wire:click='$emitTo("{{ $event['to'] }}", "{{ $event['event'] }}", @json($event['params']))'
                            @endif
                            @if($action->view)
                            wire:click='$emit("openModal", "{{$action->view}}", @json($actionParameters))'
                            @endif

                            @if($ruleDisabled) disabled @endif
                            title="{{ $action->tooltip }}"
                            {{ $class }}
                        >
                            {!! $ruleCaption ?? $action->caption !!}
                        </button>
                    @endif

                    @if(filled($ruleRedirect))
                        <a @if($ruleDisabled) disabled
                           @else
                           href="{{ $ruleRedirect['url'] }}" target="{{ $ruleRedirect['target'] }}"
                           @endif
                           title="{{ $action->tooltip }}"
                           {{ $class }}>
                            {!! $ruleCaption ?? $action->caption !!}
                        </a>
                    @endif

                    @if(filled($action->route))
                        @if(strtolower($action->method) !== 'get')
                            <form target="{{ $action->target }}"
                                  action="{{ route($action->route, $actionParameters) }}"
                                  method="post">
                                @method($action->method)
                                @csrf
                                <button type="submit"
                                        title="{{ $action->tooltip }}"
                                        @if($ruleDisabled) disabled @endif {{ $class }}>
                                    {!! $ruleCaption ?? $action->caption !!}
                                </button>
                            </form>
                        @else
                            <a href="{{ route($action->route, $actionParameters) }}"
                               target="{{ $action->target }}"
                               title="{{ $action->tooltip }}"
                               {{ $class }}
                            >
                                {!! $ruleCaption ?? $action->caption !!}
                            </a>
                        @endif
                    @endif

                        @if(filled($action->bladeComponent))
                            <x-dynamic-component :component="$ruleBladeComponent ?? $action->bladeComponent"
                                                 :attributes="$attributesBag"/>
                        @endif
                </div>
            </td>
        @endforeach
    @endif
</div>
