@extends('layouts.admin')

@section('title', trans('general.title.edit', ['type' => trans_choice('general.accounts', 1)]))

@section('content')
    <!-- Default box -->
    <div class="box box-success">
        {!! Form::model($account, [
            'method' => 'PATCH',
            'url' => ['banking/accounts', $account->id],
            'role' => 'form'
        ]) !!}

        <div class="box-body">
            {{ Form::textGroup('name', trans('general.name'), 'id-card-o') }}

            {{ Form::textGroup('number', trans('accounts.number'), 'pencil') }}

            {{ Form::selectGroup('currency_code', trans_choice('general.currencies', 1), 'exchange', $currencies) }}

            {{ Form::textGroup('opening_balance', trans('accounts.opening_balance'), 'money') }}

            {{ Form::textGroup('bank_name', trans('accounts.bank_name'), 'university', []) }}

            {{ Form::textGroup('bank_phone', trans('accounts.bank_phone'), 'phone', []) }}

            {{ Form::textareaGroup('bank_address', trans('accounts.bank_address')) }}

            {{ Form::radioGroup('default_account', trans('accounts.default_account')) }}

            {{ Form::radioGroup('enabled', trans('general.enabled')) }}
        </div>
        <!-- /.box-body -->

        @permission('update-banking-accounts')
        <div class="box-footer">
            {{ Form::saveButtons('banking/accounts') }}
        </div>
        <!-- /.box-footer -->
        @endpermission

        {!! Form::close() !!}
    </div>
@endsection

@push('scripts')
    <script type="text/javascript">
        var text_yes = '{{ trans('general.yes') }}';
        var text_no = '{{ trans('general.no') }}';

        $(document).ready(function(){
            $("#opening_balance").maskMoney({
                thousands : '{{ $currency->thousands_separator }}',
                decimal : '{{ $currency->decimal_mark }}',
                precision : {{ $currency->precision }},
                allowZero : true,
                @if($currency->symbol_first)
                prefix : '{{ $currency->symbol }}'
                @else
                suffix : '{{ $currency->symbol }}'
                @endif
            });

            $("#opening_balance").focusout();

            $("#currency_code").select2({
                placeholder: "{{ trans('general.form.select.field', ['field' => trans_choice('general.currencies', 1)]) }}"
            });
        });

        $(document).on('change', '#currency_code', function (e) {
            $.ajax({
                url: '{{ url("settings/currencies/currency") }}',
                type: 'GET',
                dataType: 'JSON',
                data: 'code=' + $(this).val(),
                success: function(data) {
                    $('#currency').val(data.currency_code);

                    $('#currency_code').val(data.currency_code);
                    $('#currency_rate').val(data.currency_rate);

                    opening_balance = $('#opening_balance').maskMoney('unmasked')[0];

                    $("#opening_balance").maskMoney({
                        thousands : data.thousands_separator,
                        decimal : data.decimal_mark,
                        precision : data.precision,
                        allowZero : true,
                        prefix : (data.symbol_first) ? data.symbol : '',
                        suffix : (data.symbol_first) ? '' : data.symbol
                    });

                    $('#opening_balance').val(opening_balance);

                    $('#opening_balance').trigger('focus');
                }
            });
        });
    </script>
@endpush
