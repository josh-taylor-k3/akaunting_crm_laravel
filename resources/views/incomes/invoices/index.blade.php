@extends('layouts.admin')

@section('title', trans_choice('general.invoices', 2))

@section('new_button')
    @permission('create-incomes-invoices')
        <span><a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm btn-success header-button-top"><span class="fa fa-plus"></span> &nbsp;{{ trans('general.add_new') }}</a></span>
        <span><a href="{{ url('common/import/incomes/invoices') }}" class="btn btn-white btn-sm header-button-top"><span class="fa fa-upload"></span> &nbsp;{{ trans('import.import') }}</a></span>
    @endpermission
    <span><a href="{{ route('invoices.export', request()->input()) }}" class="btn btn-white btn-sm header-button-top"><span class="fa fa-download"></span> &nbsp;{{ trans('general.export') }}</a></span>
@endsection

@section('content')
    @if ($invoices->count())
        <div class="card">
            <div class="card-header border-bottom-0" v-bind:class="[bulk_action.show ? 'bg-gradient-primary' : '']">
                {!! Form::open([
                    'url' => 'incomes/invoices',
                    'role' => 'form',
                    'method' => 'GET',
                    'class' => 'mb-0'
                ]) !!}
                    <div class="row" v-if="!bulk_action.show">
                        <div class="col-12 d-flex align-items-center">
                            <span class="font-weight-400 d-none d-lg-block mr-2">{{ trans('general.search') }}:</span>
                            <akaunting-search></akaunting-search>
                        </div>
                    </div>

                    {{ Form::bulkActionRowGroup('general.invoices', $bulk_actions, 'incomes/invoices') }}
                {!! Form::close() !!}
            </div>

            <div class="table-responsive">
                <table class="table table-flush table-hover">
                    <thead class="thead-light">
                        <tr class="row table-head-line">
                            <th class="col-sm-2 col-md-1 col-lg-1 col-xl-1 d-none d-sm-block">{{ Form::bulkActionAllGroup() }}</th>
                            <th class="col-md-2 col-lg-2 col-xl-2 d-none d-md-block">@sortablelink('invoice_number', trans_choice('general.numbers', 1), ['filter' => 'active, visible'], ['class' => 'col-aka', 'rel' => 'nofollow'])</th>
                            <th class="col-xs-4 col-sm-4 col-md-4 col-lg-2 col-xl-2">@sortablelink('contact_name', trans_choice('general.customers', 1))</th>
                            <th class="col-xs-4 col-sm-4 col-md-3 col-lg-1 col-xl-1 text-right">@sortablelink('amount', trans('general.amount'))</th>
                            <th class="col-lg-2 col-xl-2 d-none d-lg-block">@sortablelink('invoiced_at', trans('invoices.invoice_date'))</th>
                            <th class="col-lg-2 col-xl-2 d-none d-lg-block">@sortablelink('due_at', trans('invoices.due_date'))</th>
                            <th class="col-lg-1 col-xl-1 d-none d-lg-block">@sortablelink('invoice_status_code', trans_choice('general.statuses', 1))</th>
                            <th class="col-xs-4 col-sm-2 col-md-2 col-lg-1 col-xl-1 text-center"><a>{{ trans('general.actions') }}</a></th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($invoices as $item)
                            @php $paid = $item->paid; @endphp
                            <tr class="row align-items-center border-top-1">
                                <td class="col-sm-2 col-md-1 col-lg-1 col-xl-1 d-none d-sm-block">{{ Form::bulkActionGroup($item->id, $item->invoice_number) }}</td>
                                <td class="col-md-2 col-lg-2 col-xl-2 d-none d-md-block"><a class="col-aka text-success" href="{{ route('invoices.show' , $item->id) }}">{{ $item->invoice_number }}</a></td>
                                <td class="col-xs-4 col-sm-4 col-md-4 col-lg-2 col-xl-2">{{ $item->contact_name }}</td>
                                <td class="col-xs-4 col-sm-4 col-md-3 col-lg-1 col-xl-1 text-right">@money($item->amount, $item->currency_code, true)</td>
                                <td class="col-lg-2 col-xl-2 d-none d-lg-block">@date($item->invoiced_at)</td>
                                <td class="col-lg-2 col-xl-2 d-none d-lg-block">@date($item->due_at)</td>
                                <td class="col-lg-1 col-xl-1 d-none d-lg-block">
                                    <span class="badge badge-pill badge-{{ $item->status->label }}">{{ trans('invoices.status.' . $item->status->code) }}</span>
                                </td>
                                <td class="col-xs-4 col-sm-2 col-md-2 col-lg-1 col-xl-1 text-center">
                                    <div class="dropdown">
                                        <a class="btn btn-neutral btn-sm text-light items-align-center py-2" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-ellipsis-h text-muted"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                            <a class="dropdown-item" href="{{ route('invoices.show', $item->id) }}">{{ trans('general.show') }}</a>
                                            @if (!$item->reconciled)
                                                <a class="dropdown-item" href="{{ route('invoices.edit', $item->id) }}">{{ trans('general.edit') }}</a>
                                            @endif
                                            <div class="dropdown-divider"></div>

                                            @permission('create-incomes-invoices')
                                                <a class="dropdown-item" href="{{ route('invoices.duplicate', $item->id) }}">{{ trans('general.duplicate') }}</a>
                                                <div class="dropdown-divider"></div>
                                            @endpermission

                                            @permission('delete-incomes-invoices')
                                                @if (!$item->reconciled)
                                                    {!! Form::deleteLink($item, 'incomes/invoices') !!}
                                                @endif
                                            @endpermission
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer table-action">
                <div class="row">
                    @include('partials.admin.pagination', ['items' => $invoices])
                </div>
            </div>
        </div>
    @else
        @include('partials.admin.empty_page', ['page' => 'invoices', 'docs_path' => 'incomes/invoices'])
    @endif
@endsection

@push('scripts_start')
    <script src="{{ asset('public/js/incomes/invoices.js?v=' . version('short')) }}"></script>
@endpush
