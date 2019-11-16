<?php

namespace App\Jobs\Banking;

use App\Abstracts\Job;
use App\Models\Banking\Reconciliation;

class CreateReconciliation extends Job
{
    protected $request;

    /**
     * Create a new job instance.
     *
     * @param  $request
     */
    public function __construct($request)
    {
        $this->request = $this->getRequestInstance($request);
    }

    /**
     * Execute the job.
     *
     * @return Reconciliation
     */
    public function handle()
    {
        $reconcile = $this->request->get('reconcile');
        $transactions = $this->request->get('transactions');

        $reconciliation = Reconciliation::create([
            'company_id' => session('company_id'),
            'account_id' => $this->request->get('account_id'),
            'started_at' => $this->request->get('started_at'),
            'ended_at' => $this->request->get('ended_at'),
            'closing_balance' => $this->request->get('closing_balance'),
            'reconciled' => $reconcile ? 1 : 0,
        ]);

        if ($transactions) {
            foreach ($transactions as $key => $value) {
                $t = explode('_', $key);
                $m = '\\' . $t['1'];

                $transaction = $m::find($t[0]);
                $transaction->reconciled = 1;
                $transaction->save();
            }
        }

        return $reconciliation;
    }
}
