<?php

namespace App\Models\Setting;

use App\Models\Model;
use App\Models\Item\Item;
use App\Models\Expense\Payment;
use App\Models\Income\Revenue;

class Category extends Model
{
    protected $table = 'categories';

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['company_id', 'name', 'type', 'color', 'enabled'];

    /**
     * Sortable columns.
     *
     * @var array
     */
    public $sortable = ['name', 'type', 'enabled'];

    public function revenues()
    {
        return $this->hasMany('App\Models\Income\Revenue');
    }

    public function payments()
    {
        return $this->hasMany('App\Models\Expense\Payment');
    }

    public function items()
    {
        return $this->hasMany('App\Models\Item\Item');
    }

    public function canDelete()
    {
        $error = false;

        if ($items = $this->items()->count()) {
            $error['items'] = $items;
        }

        if ($payments = $this->payments()->count()) {
            $error['payments'] = $payments;
        }

        if ($revenues = $this->revenues()->count()) {
            $error['revenues'] = $revenues;
        }

        if ($error) {
            return $error;
        }

        return true;
    }

    /**
     * Scope to only include categories of a given type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }
}
