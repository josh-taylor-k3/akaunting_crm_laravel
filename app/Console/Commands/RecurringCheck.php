<?php

namespace App\Console\Commands;

use App\Models\Company\Company;
use App\Models\Common\Recurring;
use App\Models\Expense\Bill;
use App\Models\Expense\BillHistory;
use App\Models\Expense\Payment;
use App\Models\Income\Invoice;
use App\Models\Income\InvoiceHistory;
use App\Models\Income\Revenue;
use App\Notifications\Expense\Bill as BillNotification;
use App\Notifications\Income\Invoice as InvoiceNotification;
use App\Traits\Incomes;
use App\Utilities\Overrider;
use Date;
use Illuminate\Console\Command;
use Recurr\Rule;
use Recurr\Transformer\ArrayTransformer;
use Recurr\Transformer\ArrayTransformerConfig;

class RecurringCheck extends Command
{
    use Incomes;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recurring:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for recurring';
    
    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->today = Date::today();

        // Get all companies
        $companies = Company::all();

        foreach ($companies as $company) {
            // Set company id
            session(['company_id' => $company->id]);

            // Override settings and currencies
            Overrider::load('settings');
            Overrider::load('currencies');

            $company->setSettings();

            $recurring = $company->recurring();

            foreach ($recurring as $recur) {
                $current = Date::parse($recur->schedule()->current()->getStart()->format('Y-m-d'));

                // Check if should recur today
                if ($this->today->ne($current)) {
                    continue;
                }

                $type = end(explode('\\', $recur->recurable_type));

                $model = $type::find($recur->recurable_id);

                if (!$model) {
                    continue;
                }

                switch ($type) {
                    case 'Bill':
                        $this->recurBill($company, $model);
                        break;
                    case 'Invoice':
                        $this->recurInvoice($company, $model);
                        break;
                    case 'Payment':
                    case 'Revenue':
                        // Create new record
                        $clone = $model->duplicate();

                        // Update date
                        $clone->paid_at = $this->today->format('Y-m-d');
                        $clone->save();
                        break;
                }
            }
        }

        // Unset company_id
        session()->forget('company_id');
    }

    protected function recurInvoice($company, $model)
    {
        // Create new record
        $clone = $model->duplicate();

        // Days between invoiced and due date
        $diff_days = Date::parse($clone->due_at)->diffInDays(Date::parse($clone->invoiced_at));

        // Update dates
        $clone->invoiced_at = $this->today->format('Y-m-d');
        $clone->due_at = $this->today->addDays($diff_days)->format('Y-m-d');
        $clone->save();

        // Add invoice history
        InvoiceHistory::create([
            'company_id' => session('company_id'),
            'invoice_id' => $clone->id,
            'status_code' => 'draft',
            'notify' => 0,
            'description' => trans('messages.success.added', ['type' => $clone->invoice_number]),
        ]);

        // Notify the customer
        if ($clone->customer && !empty($clone->customer_email)) {
            $clone->customer->notify(new InvoiceNotification($clone));
        }

        // Notify all users assigned to this company
        foreach ($company->users as $user) {
            if (!$user->can('read-notifications')) {
                continue;
            }

            $user->notify(new InvoiceNotification($clone));
        }

        // Update next invoice number
        $this->increaseNextInvoiceNumber();
    }

    protected function recurBill($company, $model)
    {
        // Create new record
        $clone = $model->duplicate();

        // Days between invoiced and due date
        $diff_days = Date::parse($clone->due_at)->diffInDays(Date::parse($clone->invoiced_at));

        // Update dates
        $clone->billed_at = $this->today->format('Y-m-d');
        $clone->due_at = $this->today->addDays($diff_days)->format('Y-m-d');
        $clone->save();

        // Add bill history
        BillHistory::create([
            'company_id' => session('company_id'),
            'bill_id' => $clone->id,
            'status_code' => 'draft',
            'notify' => 0,
            'description' => trans('messages.success.added', ['type' => $clone->bill_number]),
        ]);

        // Notify all users assigned to this company
        foreach ($company->users as $user) {
            if (!$user->can('read-notifications')) {
                continue;
            }

            $user->notify(new BillNotification($clone));
        }
    }
}
