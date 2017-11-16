<?php

namespace App\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\InvoicePayment as PaymentRequest;
use App\Models\Banking\Account;
use App\Models\Income\Customer;
use App\Models\Income\Invoice;
use App\Models\Income\InvoiceStatus;
use App\Models\Setting\Category;
use App\Models\Setting\Currency;
use App\Traits\Currencies;
use App\Traits\DateTime;
use App\Traits\Uploads;
use Auth;
use Jenssegers\Date\Date;

use App\Events\PaymentGatewayConfirm;

use App\Utilities\Modules;

class Invoices extends Controller
{
    use DateTime, Currencies, Uploads;

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $invoices = Invoice::with('status')->where('customer_id', '=', Auth::user()->customer->id)->paginate();

        foreach ($invoices as $invoice) {
            $paid = 0;

            foreach ($invoice->payments as $item) {
                $item->default_currency_code = $invoice->currency_code;

                $paid += $item->getDynamicConvertedAmount();
            }

            $invoice->amount = $invoice->amount - $paid;
        }

        $status = collect(InvoiceStatus::all()->pluck('name', 'code'))
            ->prepend(trans('general.all_type', ['type' => trans_choice('general.statuses', 2)]), '');

        return view('customers.invoices.index', compact('invoices', 'status'));
    }

    /**
     * Show the form for viewing the specified resource.
     *
     * @param  Invoice  $invoice
     *
     * @return Response
     */
    public function show(Invoice $invoice)
    {
        $sub_total = 0;
        $tax_total = 0;
        $paid = 0;

        foreach ($invoice->items as $item) {
            $sub_total += ($item->price * $item->quantity);
            $tax_total += ($item->tax * $item->quantity);
        }

        foreach ($invoice->payments as $item) {
            $item->default_currency_code = $invoice->currency_code;

            $paid += $item->getDynamicConvertedAmount();
        }

        $invoice->sub_total = $sub_total;
        $invoice->tax_total = $tax_total;
        $invoice->paid = $paid;
        $invoice->grand_total = (($sub_total + $tax_total) - $paid);

        $accounts = Account::enabled()->pluck('name', 'id');

        $currencies = Currency::enabled()->pluck('name', 'code')->toArray();

        $account_currency_code = Account::where('id', setting('general.default_account'))->pluck('currency_code')->first();

        $customers = Customer::enabled()->pluck('name', 'id');

        $categories = Category::enabled()->type('income')->pluck('name', 'id');

        $payment_methods = Modules::getPaymentMethods();

        return view('customers.invoices.show', compact('invoice', 'accounts', 'currencies', 'account_currency_code', 'customers', 'categories', 'payment_methods'));
    }

    /**
     * Show the form for viewing the specified resource.
     *
     * @param  int  $invoice_id
     *
     * @return Response
     */
    public function printInvoice($invoice_id)
    {
        $sub_total = 0;
        $tax_total = 0;
        $paid = 0;

        $invoice = Invoice::where('id', $invoice_id)->first();

        foreach ($invoice->items as $item) {
            $sub_total += ($item->price * $item->quantity);
            $tax_total += ($item->tax * $item->quantity);
        }

        foreach ($invoice->payments as $item) {
            $item->default_currency_code = $invoice->currency_code;

            $paid += $item->getDynamicConvertedAmount();
        }

        $invoice->sub_total = $sub_total;
        $invoice->tax_total = $tax_total;
        $invoice->paid = $paid;
        $invoice->grand_total = (($sub_total + $tax_total) - $paid);

        return view('customers.invoices.invoice', compact('invoice'));
    }

    /**
     * Show the form for viewing the specified resource.
     *
     * @param  int  $invoice_id
     *
     * @return Response
     */
    public function pdfInvoice($invoice_id)
    {
        $sub_total = 0;
        $tax_total = 0;
        $paid = 0;

        $invoice = Invoice::where('id', $invoice_id)->first();

        foreach ($invoice->items as $item) {
            $sub_total += ($item->price * $item->quantity);
            $tax_total += ($item->tax * $item->quantity);
        }

        foreach ($invoice->payments as $item) {
            $item->default_currency_code = $invoice->currency_code;

            $paid += $item->getDynamicConvertedAmount();
        }

        $invoice->sub_total = $sub_total;
        $invoice->tax_total = $tax_total;
        $invoice->paid = $paid;
        $invoice->grand_total = (($sub_total + $tax_total) - $paid);

        $html = view('incomes.invoices.invoice', compact('invoice'))->render();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        $file_name = 'invoice_'.time().'.pdf';

        return $pdf->download($file_name);
    }

    /**
     * Show the form for viewing the specified resource.
     *
     * @param  PaymentRequest  $request
     *
     * @return Response
     */
    public function payment(PaymentRequest $request)
    {
        $invoice = Invoice::where(['id' => $request['invoice_id'], 'customer_id' => Auth::user()->customer->id])->first();

        // Fire the event to extend the menu
        $result = event(new PaymentGatewayConfirm($request['payment_method'], $invoice));

        return response()->json($result);
    }
}
