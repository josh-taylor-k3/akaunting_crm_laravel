@stack($name . '_input_start')
    <akaunting-select
        class="{{ $col }} {{ isset($attributes['required']) ? 'required' : '' }}"
        :form-classes="[{'has-error': {{ isset($attributes['v-error']) ? $attributes['v-error'] : 'form.errors.get("' . $name . '")' }} }]"
        :title="'{{ $text }}'"
        :placeholder="'{{ trans('general.form.select.field', ['field' => $text]) }}'"
        :name="'{{ $name }}'"
        :options="{{ json_encode($values) }}"
        :value="'{{ old($name, $selected) }}'"
        :icon="'{{ $icon }}'"
        :group="true"
        @interface="{{ !empty($attributes['v-model']) ? $attributes['v-model']. ' = $event' : 'form.' . $name . ' = $event' }}"
        @if (!empty($attributes['change']))
        @change="{{ $attributes['change'] }}($event)"
        @endif
        :form-error="{{ isset($attributes['v-error-message']) ? $attributes['v-error-message'] : 'form.errors.get("' . $name . '")' }}"
        :no-data-text="'{{ trans('general.no_data') }}'"
        :no-matching-data-text="'{{ trans('general.no_matching_data') }}'"
    ></akaunting-select>
@stack($name . '_input_end')
