@extends('layouts.print')

@section('title', trans_choice('general.invoices', 1) . ': ' . $invoice->invoice_number)

@section('content')
    <div class="row" style="background-color:{{ setting('invoice.color') }};">
        <div class="col-58 m-first-column">
            <div class="text company pl-2 m-fc-left">
                <img src="{{ $logo }}" class="m-logo" alt="{{ setting('company.name') }}"/>
            </div>
            <div class="text company m-fc-right">
                <strong class="text-white">{{ setting('company.name') }}</strong>
            </div>
        </div>

        <div class="col-42">
            <div class="text company">
                <br>
                <strong class="text-white">{!! nl2br(setting('company.address')) !!}</strong><br><br>

                <strong class="text-white">
                    @if (setting('company.tax_number'))
                        {{ trans('general.tax_number') }}: {{ setting('company.tax_number') }}
                    @endif
                </strong><br><br>

                <strong class="text-white">
                    @if (setting('company.phone'))
                        {{ setting('company.phone') }}
                    @endif
                </strong><br><br>

                <strong class="text-white">{{ setting('company.email') }}</strong><br><br>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-58">
            <div class="text company">
                <br>
                <strong>{{ trans('invoices.bill_to') }}</strong><br>
                @stack('name_input_start')
                    <strong>{{ $invoice->contact_name }}</strong><br><br>
                @stack('name_input_end')

                @stack('address_input_start')
                    {!! nl2br($invoice->contact_address) !!}<br><br>
                @stack('address_input_end')

                @stack('tax_number_input_start')
                    @if ($invoice->contact_tax_number)
                       {{ trans('general.tax_number') }}: {{ $invoice->contact_tax_number }}<br><br>
                    @endif
                @stack('tax_number_input_end')

                @stack('phone_input_start')
                    @if ($invoice->contact_phone)
                        {{ $invoice->contact_phone }}<br><br>
                    @endif
                @stack('phone_input_end')

                @stack('email_start')
                    {{ $invoice->contact_email }}<br><br>
                @stack('email_input_end')
            </div>
        </div>

        <div class="col-42">
            <div class="text company">
                @stack('order_number_input_start')
                    @if ($invoice->order_number)
                        <p>
                            <b>{{ trans('invoices.order_number') }}:</b>
                            {{ $invoice->order_number }}
                        </p>
                    @endif
                @stack('order_number_input_end')

                @stack('invoice_number_input_start')
                    <p>
                        <b>{{ trans('invoices.invoice_number') }}:</b>
                        {{ $invoice->invoice_number }}
                    </p>
                @stack('invoice_number_input_end')

                @stack('invoiced_at_input_start')
                    <p>
                        <b>{{ trans('invoices.invoice_date') }}:</b>
                        @date($invoice->invoiced_at)
                    </p>
                @stack('invoiced_at_input_end')

                @stack('due_at_input_start')
                    <p>
                        <b>{{ trans('invoices.payment_due') }}:</b>
                        @date($invoice->due_at)
                    </p>
                @stack('due_at_input_end')
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-100">
            <div class="text">
                <table class="m-lines">
                    <thead style="background-color:{{ setting('invoice.color') }};">
                        <tr>
                            @stack('name_th_start')
                                <th class="item text-white">{{ trans_choice($text_override['items'], 2) }}</th>
                            @stack('name_th_end')

                            @stack('quantity_th_start')
                                <th class="quantity text-white">{{ trans($text_override['quantity']) }}</th>
                            @stack('quantity_th_end')

                            @stack('price_th_start')
                                <th class="price text-white">{{ trans($text_override['price']) }}</th>
                            @stack('price_th_end')

                            @stack('total_th_start')
                                <th class="total text-white">{{ trans('invoices.total') }}</th>
                            @stack('total_th_end')
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->items as $item)
                            <tr>
                                @stack('name_td_start')
                                    <td class="item">
                                        {{ $item->name }}
                                        @if ($item->desc)
                                            {!! $item->desc !!}
                                        @endif
                                    </td>
                                @stack('name_td_end')

                                @stack('quantity_td_start')
                                    <td class="quantity">{{ $item->quantity }}</td>
                                @stack('quantity_td_end')

                                @stack('price_td_start')
                                    <td class="price">@money($item->price, $invoice->currency_code, true)</td>
                                @stack('price_td_end')

                                @stack('total_td_start')
                                    <td class="total">@money($item->total, $invoice->currency_code, true)</td>
                                @stack('total_td_end')
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row mt-7">
        <div class="col-58">
            <div class="text company">
                @stack('notes_input_start')
                    @if ($invoice->notes)
                        <strong>{{ trans_choice('general.notes', 2) }}</strong><br><br>
                        <div class="border-1 py-1 border-radius-default pl-2 m-note">
                            {{ $invoice->notes }}
                        </div>
                    @endif
                @stack('notes_input_end')
            </div>
        </div>

        <div class="col-42 text-right">
            <div class="text company pr-2">
                @foreach ($invoice->totals as $total)
                    @if ($total->code != 'total')
                        @stack($total->code . '_td_start')
                            <strong>{{ trans($total->title) }}:</strong>
                            @money($total->amount, $invoice->currency_code, true)<br><br>
                        @stack($total->code . '_td_end')
                    @else
                        @if ($invoice->paid)
                            <strong>{{ trans('invoices.paid') }}:</strong>
                            - @money($invoice->paid, $invoice->currency_code, true)</strong><br>
                        @endif
                        @stack('grand_total_td_start')
                            <strong>{{ trans($total->name) }}:</strong>
                            @money($total->amount - $invoice->paid, $invoice->currency_code, true)
                        @stack('grand_total_td_end')
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    @if ($invoice->footer)
        <div class="row mt-7">
            <div class="col-100 py-2" style="background-color:{{ setting('invoice.color') }};">
                <div class="text company pl-2">
                    <strong class="text-white">{!! $invoice->footer !!}</strong>
                </div>
            </div>
        </div>
    @endif
@endsection
