@extends('layouts.admin')

@section('title', trans('general.title.new', ['type' => trans_choice('general.reconciliations', 1)]))

@section('content')
    <div class="card">
        {!! Form::open([
            'url' => 'banking/reconciliations/create',
            'id' => 'reconciliation',
            '@submit.prevent' => 'onSubmit',
            '@keydown' => 'form.errors.clear($event.target.name)',
            'files' => true,
            'role' => 'form',
            'class' => 'form-loading-button mb-0',
            'novalidate' => true
        ]) !!}

            <div class="card-body">
                <div class="form-row align-items-center">
                    {{ Form::dateGroup('started_at', trans('reconciliations.start_date'), 'calendar',['id' => 'started_at', 'class' => 'form-control datepicker', 'required' => 'required', 'date-format' => 'Y-m-d', 'autocomplete' => 'off'], request('started_at'), 'col-xl-3') }}

                    {{ Form::dateGroup('ended_at', trans('reconciliations.end_date'), 'calendar',['id' => 'ended_at', 'class' => 'form-control datepicker', 'required' => 'required', 'date-format' => 'Y-m-d', 'autocomplete' => 'off'], request('started_at'), 'col-xl-3') }}

                    {{ Form::moneyGroup('closing_balance', trans('reconciliations.closing_balance'), 'balance-scale', ['required' => 'required', 'autofocus' => 'autofocus', 'currency' => $currency], '0', 'col-xl-2') }}

                    {{ Form::selectGroup('account_id', trans_choice('general.accounts', 1), 'university', $accounts, request('account_id', setting('default.account')), ['required' => 'required'], 'col-xl-2') }}

                    <div class="col-xl-2">
                        <div class="input-group mt-1">
                            {!! Form::button('<span class="fa fa-list"></span> &nbsp;' . trans('reconciliations.transactions'), ['type' => 'submit', 'class' => 'btn btn-success header-button-top']) !!}
                        </div>
                    </div>
                </div>
            </div>

        {!! Form::close() !!}
    </div>

    <div class="card">
        <div class="card-header border-0">
            <h3 class="mb-0">{{ trans_choice('general.transactions', 2) }}</h3>
        </div>

        {!! Form::open([
            'url' => 'banking/reconciliations',
            'role' => 'form',
            'class' => 'form-loading-button',
            'id' => 'form-reconciliations',
            'class' => 'mb-0'
        ]) !!}

            {{ Form::hidden('account_id', $account->id) }}
            {{ Form::hidden('currency_code', $currency->code, ['id' => 'currency_code']) }}
            {{ Form::hidden('opening_balance', $opening_balance, ['id' => 'opening_balance']) }}
            {{ Form::hidden('closing_balance', request('closing_balance', '0'), ['id' => 'closing_balance']) }}
            {{ Form::hidden('started_at', request('started_at')) }}
            {{ Form::hidden('ended_at', request('ended_at')) }}
            {{ Form::hidden('reconcile', '0', ['id' => 'hidden-reconcile']) }}

            <div class="table-responsive">
                <table class="table table-flush table-hover">
                    <thead class="thead-light">
                        <tr class="row table-head-line">
                            <th class="col-xs-4 col-sm-3 col-md-2 long-texts">{{ trans('general.date') }}</th>
                            <th class="col-md-2 text-center d-none d-md-block">{{ trans('general.description') }}</th>
                            <th class="col-md-2 col-sm-3 col-md-3 d-none d-sm-block">{{ trans_choice('general.contacts', 1) }}</th>
                            <th class="col-xs-4 col-sm-3 col-md-2 text-right">{{ trans('reconciliations.deposit') }}</th>
                            <th class="col-xs-4 col-sm-3 col-md-2 text-right long-texts">{{ trans('reconciliations.withdrawal') }}</th>
                            <th class="col-md-1 text-right d-none d-md-block">{{ trans('general.clear') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($transactions as $item)
                            <tr class="row align-items-center border-top-1">
                                <td class="col-xs-4 col-sm-3 col-md-2 long-texts">@date($item->paid_at)</td>
                                <td class="col-md-2 text-center d-none d-md-block">{{ $item->description }}</td>
                                <td class="col-sm-3 col-md-3 d-none d-sm-block">{{ $item->contact->name }}</td>
                                @if ($item->type == 'income')
                                    <td class="col-xs-4 col-sm-3 col-md-2 text-right">@money($item->amount, $item->currency_code, true)</td>
                                    <td class="col-xs-4 col-sm-3 col-md-2 text-right">N/A</td>
                                @else
                                    <td class="col-xs-4 col-sm-3 col-md-2 text-right">N/A</td>
                                    <td class="col-xs-4 col-sm-3 col-md-2 text-right">@money($item->amount, $item->currency_code, true)</td>
                                @endif
                                <td class="col-md-1 text-right d-none d-md-block">{{ Form::checkbox('transactions['. $item->id . '_'. $item->type . ']', $item->amount, $item->reconciled) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if ($transactions->count())
                    <table class="table">
                        <tbody>
                            <tr class="row">
                                <th class="col-md-9 col-lg-11 text-right d-none d-md-block">{{ trans('reconciliations.closing_balance') }}:</th>
                                <td id="closing-balance" class="col-md-3 col-lg-1 text-right d-none d-md-block">@money(request('closing_balance', '0'), $account->currency_code, true)</td>
                            </tr>
                            <tr class="row">
                                <th class="col-md-9 col-lg-11 text-right d-none d-md-block">{{ trans('reconciliations.cleared_amount') }}:</th>
                                <td id="cleared-amount" class="col-md-3 col-lg-1 text-right d-none d-md-block">@money('0', $account->currency_code, true)</td>
                            </tr>
                            <tr class="row">
                                <th class="col-md-9 col-lg-11 text-right d-none d-md-block">{{ trans('general.difference') }}:</th>
                                <td id="difference" class="col-md-3 col-lg-1 text-right d-none d-md-block">@money('0', $account->currency_code, true)</td>
                            </tr>
                        </tbody>
                    </table>
                @endif
            </div>

            <div class="card-footer">
                <div class="row">
                    <div class="col-md-12">
                        @if ($transactions->count())
                            <div class="float-right">
                                <a href="{{ route('reconciliations.index') }}" class="btn btn-outline-secondary header-button-top"><span class="fa fa-times"></span> &nbsp;{{ trans('general.cancel') }}</a>
                                {!! Form::button(
                                    '<div v-if="form.loading" class="aka-loader-frame"><div class="aka-loader"></div></div> <span v-if="!form.loading" class="btn-inner--icon"><i class="fas fa-check"></i></span>' . '<span v-if="!form.loading" class="btn-inner--text"> ' . trans('reconciliations.reconcile') . '</span>',
                                    [':disabled' => 'form.loading', 'type' => 'submit', 'class' => 'btn btn-icon btn-info button-submit header-button-top', 'data-loading-text' => trans('general.loading')]) !!}

                                {!! Form::button(
                                    '<div v-if="form.loading" class="aka-loader-frame"><div class="aka-loader"></div></div> <span v-if="!form.loading" class="btn-inner--icon"><i class="fas fa-save"></i></span>' . '<span v-if="!form.loading" class="btn-inner--text"> ' . trans('general.save') . '</span>',
                                    [':disabled' => 'form.loading', 'type' => 'submit', 'class' => 'btn btn-icon btn-success button-submit header-button-top', 'data-loading-text' => trans('general.loading')]) !!}
                            </div>
                        @else
                            <div class="text-sm text-muted" id="datatable-basic_info" role="status" aria-live="polite">
                                <small>{{ trans('general.no_records') }}</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

        {!! Form::close() !!}
    </div>
@endsection

@push('scripts_start')
    <script src="{{ asset('public/js/banking/reconciliations.js?v=' . version('short')) }}"></script>
@endpush
