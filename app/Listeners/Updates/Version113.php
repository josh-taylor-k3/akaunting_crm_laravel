<?php

namespace App\Listeners\Updates;

use App\Events\UpdateFinished;
use App\Models\Setting\Currency;

class Version113 extends Listener
{
    const ALIAS = 'core';

    const VERSION = '1.1.3';

    /**
     * Handle the event.
     *
     * @param  $event
     * @return void
     */
    public function handle(UpdateFinished $event)
    {
        // Check if should listen
        if (!$this->check($event)) {
            return;
        }

        // Update currencies
        $currencies = Currency::all();

        foreach ($currencies as $currency) {
            $currency->precision = config('money.' . $currency->code . '.precision');
            $currency->symbol = config('money.' . $currency->code . '.symbol');
            $currency->symbol_first = config('money.' . $currency->code . '.symbol_first') ? 1 : 0;
            $currency->decimal_mark = config('money.' . $currency->code . '.decimal_mark');
            $currency->thousands_separator = config('money.' . $currency->code . '.thousands_separator');

            $currency->save();
        }

    }
}
