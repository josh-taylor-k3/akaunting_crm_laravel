<div class="row border-bottom-1">
    <div class="col-58">
        <div class="text company">
            @stack('company_logo_start')
            @if (!$hideCompanyLogo)
                @if (!empty($document->contact->logo) && !empty($document->contact->logo->id))
                    <img class="d-logo" src="{{ Storage::url($document->contact->logo->id) }}" height="128" width="128" alt="{{ $document->contact_name }}"/>
                @else
                    <img class="d-logo" src="{{ $logo }}" alt="{{ setting('company.name') }}"/>
                @endif
            @endif
            @stack('company_logo_end')
        </div>
    </div>

    <div class="col-42">
        <div class="text company">
            @stack('company_details_start')
            @if (!$hideCompanyDetails)
                @if (!$hideCompanyName)
                    <strong>{{ setting('company.name') }}</strong><br>
                @endif

                @if (!$hideCompanyAddress)
                    <p>{!! nl2br(setting('company.address')) !!}</p>
                @endif

                @if (!$hideCompanyTaxNumber)
                    <p>
                        @if (setting('company.tax_number'))
                            {{ trans('general.tax_number') }}: {{ setting('company.tax_number') }}
                        @endif
                    </p>
                @endif

                @if (!$hideCompanyPhone)
                    <p>
                        @if (setting('company.phone'))
                            {{ setting('company.phone') }}
                        @endif
                    </p>
                @endif

                @if (!$hideCompanyEmail)
                    <p>{{ setting('company.email') }}</p>
                @endif
            @endif
            @stack('company_details_end')
        </div>
    </div>
</div>

<div class="row">
    <div class="col-58">
        <div class="text company">
            <br>
            @if ($hideContactInfo)
                <strong>{{ $textContactInfo }}</strong><br>
            @endif

            @stack('name_input_start')
                @if (!$hideContactName)
                    <strong>{{ $document->contact_name }}</strong><br>,
                @endif
            @stack('name_input_end')

            @stack('address_input_start')
                @if (!$hideContactAddress)
                    <p>{!! nl2br($document->contact_address) !!}</p>
                @endif
            @stack('address_input_end')

            @stack('tax_number_input_start')
                @if (!$hideContactTaxNumber)
                    <p>
                        @if ($document->contact_tax_number)
                            {{ trans('general.tax_number') }}: {{ $document->contact_tax_number }}
                        @endif
                    </p>
                @endif
            @stack('tax_number_input_end')

            @stack('phone_input_start')
                @if (!$hideContactPhone)
                    <p>
                        @if ($document->contact_phone)
                            {{ $document->contact_phone }}
                        @endif
                    </p>
                @endif
            @stack('phone_input_end')

            @stack('email_start')
                @if (!$hideContactEmail)
                    <p>
                        {{ $document->contact_email }}
                    </p>
                @endif
            @stack('email_input_end')
        </div>
    </div>

    <div class="col-42">
        <div class="text company">
            <br>
            @stack('document_number_input_start')
                @if (!$hideDocumentNumber)
                    <strong>
                        {{ $textDocumentNumber }}:
                    </strong>
                    <span class="float-right">{{ $document->document_number }}</span><br><br>
                @endif
            @stack('document_number_input_end')

            @stack('order_number_input_start')
                @if (!$hideOrderNumber)
                    @if ($document->order_number)
                        <strong>
                            {{ $textOrderNumber }}:
                        </strong>
                        <span class="float-right">{{ $document->order_number }}</span><br><br>
                    @endif
                @endif
            @stack('order_number_input_end')

            @stack('issued_at_input_start')
                @if (!$hideIssuedAt)
                    <strong>
                        {{ $textIssuedAt }}:
                    </strong>
                    <span class="float-right">@date($document->issued_at)</span><br><br>
                @endif
            @stack('issueed_at_input_end')

            @stack('due_at_input_start')
                @if (!$hideDueAt)
                    <strong>
                        {{ $textDueAt }}:
                    </strong>
                    <span class="float-right">@date($document->due_at)</span><br><br>
                @endif
            @stack('due_at_input_end')
        </div>
    </div>
</div>

<div class="row">
    <div class="col-100">
        <div class="text">
            <table class="lines">
                @foreach($document as $item)
                    <thead style="background-color:{{ $backGroundColor }} !important; -webkit-print-color-adjust: exact;">
                @endforeach
                    <tr>
                        @stack('name_th_start')
                            @if ($hideItems || (!$hideName && !$hideDescription))
                                <th class="item text-left text-white">{{ $textItems }}</th>
                            @endif
                        @stack('name_th_end')

                        @stack('quantity_th_start')
                            @if (!$hideQuantity)
                                <th class="quantity text-white">{{ $textQuantity }}</th>
                            @endif
                        @stack('quantity_th_end')

                        @stack('price_th_start')
                            @if (!$hidePrice)
                                <th class="price text-white">{{ $textPrice }}</th>
                            @endif
                        @stack('price_th_end')

                        @if (!$hideDiscount)
                            @if (in_array(setting('localisation.discount_location', 'total'), ['item', 'both']))
                                @stack('discount_td_start')
                                    <td class="discount text-white">{{ $item->discount }}</td>
                                @stack('discount_td_end')
                            @endif
                        @endif

                        @stack('total_th_start')
                            @if (!$hideAmount)
                                <th class="total text-white">{{ $textAmount }}</th>
                            @endif
                        @stack('total_th_end')
                    </tr>
                </thead>
                <tbody>
                    @foreach($document->items as $item)
                        @include('partials.documents.item.print', ['document' => $document])
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row mt-9">
    <div class="col-58">
        <div class="text company">
            @stack('notes_input_start')
                @if ($document->notes)
                    <br>
                    <strong>{{ trans_choice('general.notes', 2) }}</strong><br><br>
                    {!! nl2br($document->notes) !!}
                @endif
            @stack('notes_input_end')
        </div>
    </div>

    <div class="col-42 float-right text-right">
        <div class="text company">
            @foreach ($document->totals_sorted as $total)
                @if ($total->code != 'total')
                    @stack($total->code . '_total_tr_start')
                    <div class="border-top-1 py-2">
                        <strong class="float-left">{{ trans($total->title) }}:</strong>
                        <span>@money($total->amount, $document->currency_code, true)</span><br>
                    </div>
                    @stack($total->code . '_total_tr_end')
                @else
                    @if ($document->paid)
                        @stack('paid_total_tr_start')
                        <div class="border-top-1 py-2">
                            <strong class="float-left">{{ trans('invoices.paid') }}:</strong>
                            <span>- @money($document->paid, $document->currency_code, true)</span><br>
                        </div>
                        @stack('paid_total_tr_end')
                    @endif
                    @stack('grand_total_tr_start')
                    <div class="border-top-1 py-2">
                        <strong class="float-left">{{ trans($total->name) }}:</strong>
                        <span>@money($total->amount - $document->paid, $document->currency_code, true)</span>
                    </div>
                    @stack('grand_total_tr_end')
                @endif
            @endforeach
        </div>
    </div>
</div>

@if (!$hideFooter)
    @if ($document->footer)
        <div class="row mt-4">
            <div class="col-100 text-left">
                <div class="text company">
                    <strong>{!! nl2br($document->footer) !!}<strong>
                </div>
            </div>
        </div>
    @endif
@endif
