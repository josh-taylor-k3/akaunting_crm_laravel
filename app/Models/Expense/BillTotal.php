<?php

namespace App\Models\Expense;

use App\Abstracts\Model;
use App\Models\Setting\Tax;
use App\Traits\DateTime;

class BillTotal extends Model
{
    use DateTime;

    protected $table = 'bill_totals';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['title'];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['company_id', 'bill_id', 'code', 'name', 'amount', 'sort_order'];

    public function bill()
    {
        return $this->belongsTo('App\Models\Expense\Bill');
    }

    /**
     * Convert amount to double.
     *
     * @param  string  $value
     * @return void
     */
    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = (double) $value;
    }

    /**
     * Get the formatted name.
     *
     * @return string
     */
    public function getTitleAttribute()
    {
        $title = $this->name;

        $percent = 0;

        $tax = null;

        switch ($this->code) {
            case 'discount':
                $title = trans($title);
                $percent = $this->bill->discount;

                break;
            case 'tax':
                $tax = Tax::where('name', $title)->first();

                if (!empty($tax->rate)) {
                    $percent = $tax->rate;
                }

                break;
        }

        if (!empty($percent)) {

            $title .= ' (';

            if (setting('localisation.percent_position', 'after') == 'after') {
                $title .= $this->code == 'discount' ? false : $tax->type == 'fixed' ? $percent : $percent . '%';
            } else {
                $title .= $this->code == 'discount' ? false : $tax->type == 'fixed' ? $percent : '%' . $percent;
            }

            $title .= ')';
        }

        return $title;
    }
}
