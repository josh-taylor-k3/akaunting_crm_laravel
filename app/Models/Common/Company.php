<?php

namespace App\Models\Common;

use App\Models\Document\Document;
use App\Traits\Contacts;
use App\Traits\Media;
use App\Traits\Tenants;
use App\Traits\Transactions;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kyslik\ColumnSortable\Sortable;
use Lorisleiva\LaravelSearchString\Concerns\SearchString;

class Company extends Eloquent
{
    use Contacts, Media, SearchString, SoftDeletes, Sortable, Tenants, Transactions;

    protected $table = 'companies';

    protected $tenantable = false;

    protected $dates = ['deleted_at'];

    protected $fillable = ['domain', 'enabled'];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public $allAttributes = [
        //
    ];

    /**
     * Sortable columns.
     *
     * @var array
     */
    public $sortable = ['name', 'domain', 'email', 'enabled', 'created_at'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->allAttributes = $attributes;

        parent::__construct($attributes);
    }

    public static function boot()
    {
        parent::boot();

        static::retrieved(function($model) {
            $model->setSettings();
        });

        static::saving(function($model) {
            $model->unsetSettings();
        });
    }

    public function documents()
    {
        return $this->hasMany('App\Models\Document\Document');
    }

    public function document_histories()
    {
        return $this->hasMany('App\Models\Document\DocumentHistory');
    }

    public function document_items()
    {
        return $this->hasMany('App\Models\Document\DocumentItem');
    }

    public function document_item_taxes()
    {
        return $this->hasMany('App\Models\Document\DocumentItemTax');
    }

    public function document_totals()
    {
        return $this->hasMany('App\Models\Document\DocumentTotal');
    }

    public function accounts()
    {
        return $this->hasMany('App\Models\Banking\Account');
    }

    public function bills()
    {
        return $this->documents()->where('type', Document::BILL_TYPE);
    }

    public function bill_histories()
    {
        return $this->document_histories()->where('type', Document::BILL_TYPE);
    }

    public function bill_items()
    {
        return $this->document_items()->where('type', Document::BILL_TYPE);
    }

    public function bill_item_taxes()
    {
        return $this->document_item_taxes()->where('type', Document::BILL_TYPE);
    }

    public function bill_totals()
    {
        return $this->document_totals()->where('type', Document::BILL_TYPE);
    }

    public function categories()
    {
        return $this->hasMany('App\Models\Setting\Category');
    }

    public function contacts()
    {
        return $this->hasMany('App\Models\Common\Contact');
    }

    public function currencies()
    {
        return $this->hasMany('App\Models\Setting\Currency');
    }

    public function customers()
    {
        return $this->contacts()->whereIn('type', (array) $this->getCustomerTypes());
    }

    public function dashboards()
    {
        return $this->hasMany('App\Models\Common\Dashboard');
    }

    public function email_templates()
    {
        return $this->hasMany('App\Models\Common\EmailTemplate');
    }

    public function expense_transactions()
    {
        return $this->transactions()->whereIn('type', (array) $this->getExpenseTypes());
    }

    public function income_transactions()
    {
        return $this->transactions()->whereIn('type', (array) $this->getIncomeTypes());
    }

    public function invoices()
    {
        return $this->documents()->where('type', Document::INVOICE_TYPE);
    }

    public function invoice_histories()
    {
        return $this->document_histories()->where('type', Document::INVOICE_TYPE);
    }

    public function invoice_items()
    {
        return $this->document_items()->where('type', Document::INVOICE_TYPE);
    }

    public function invoice_item_taxes()
    {
        return $this->document_item_taxes()->where('type', Document::INVOICE_TYPE);
    }

    public function invoice_totals()
    {
        return $this->document_totals()->where('type', Document::INVOICE_TYPE);
    }

    public function items()
    {
        return $this->hasMany('App\Models\Common\Item');
    }

    public function modules()
    {
        return $this->hasMany('App\Models\Module\Module');
    }

    public function module_histories()
    {
        return $this->hasMany('App\Models\Module\ModuleHistory');
    }

    public function reconciliations()
    {
        return $this->hasMany('App\Models\Banking\Reconciliation');
    }

    public function recurring()
    {
        return $this->hasMany('App\Models\Common\Recurring');
    }

    public function reports()
    {
        return $this->hasMany('App\Models\Common\Report');
    }

    public function settings()
    {
        return $this->hasMany('App\Models\Setting\Setting');
    }

    public function taxes()
    {
        return $this->hasMany('App\Models\Setting\Tax');
    }

    public function transactions()
    {
        return $this->hasMany('App\Models\Banking\Transaction');
    }

    public function transfers()
    {
        return $this->hasMany('App\Models\Banking\Transfer');
    }

    public function users()
    {
        return $this->morphedByMany('App\Models\Auth\User', 'user', 'user_companies', 'company_id', 'user_id');
    }

    public function vendors()
    {
        return $this->contacts()->whereIn('type', (array) $this->getVendorTypes());
    }

    public function widgets()
    {
        return $this->hasMany('App\Models\Common\Widget');
    }

    public function setSettings()
    {
        $settings = $this->settings;

        $groups = [
            'company',
            'default',
        ];

        foreach ($settings as $setting) {
            list($group, $key) = explode('.', $setting->getAttribute('key'));

            // Load only general settings
            if (!in_array($group, $groups)) {
                continue;
            }

            $value = $setting->getAttribute('value');

            if (($key == 'logo') && empty($value)) {
                $value = 'public/img/company.png';
            }

            $this->setAttribute($key, $value);
        }

        // Set default default company logo if empty
        if ($this->getAttribute('logo') == '') {
            $this->setAttribute('logo', 'public/img/company.png');
        }
    }

    public function unsetSettings()
    {
        $settings = $this->settings;

        $groups = [
            'company',
            'default',
        ];

        foreach ($settings as $setting) {
            list($group, $key) = explode('.', $setting->getAttribute('key'));

            // Load only general settings
            if (!in_array($group, $groups)) {
                continue;
            }

            $this->offsetUnset($key);
        }

        $this->offsetUnset('logo');
    }

    /**
     * Scope to get all rows filtered, sorted and paginated.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $sort
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCollect($query, $sort = 'name')
    {
        $request = request();

        $search = $request->get('search');

        $query = user()->companies()->usingSearchString($search)->sortable($sort);

        if ($request->expectsJson()) {
            return $query->get();
        }

        $limit = $request->get('limit', setting('default.list_limit', '25'));

        return $query->paginate($limit);
    }

    /**
     * Scope to only include companies of a given enabled value.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeEnabled($query, $value = 1)
    {
        return $query->where('enabled', $value);
    }

    /**
     * Scope to only include companies of a given user id.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $user_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUserId($query, $user_id)
    {
        return $query->whereHas('users', function ($query) use ($user_id) {
            $query->where('user_id', $user_id);
        });
    }

    /**
     * Sort by company name
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $direction
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function nameSortable($query, $direction)
    {
        return $query->join('settings', 'companies.id', '=', 'settings.company_id')
            ->where('key', 'company.name')
            ->orderBy('value', $direction)
            ->select('companies.*');
    }

    /**
     * Sort by company email
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param $direction
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function emailSortable($query, $direction)
    {
        return $query->join('settings', 'companies.id', '=', 'settings.company_id')
            ->where('key', 'company.email')
            ->orderBy('value', $direction)
            ->select('companies.*');
    }

    /**
     * Scope autocomplete.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filter
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAutocomplete($query, $filter)
    {
        return $query->join('settings', 'companies.id', '=', 'settings.company_id')
            ->where(function ($query) use ($filter) {
                foreach ($filter as $key => $value) {
                    $column = $key;

                    if (!in_array($key, $this->fillable)) {
                        $column = 'company.' . $key;
                        $query->orWhere('key', $column);
                        $query->Where('value', 'LIKE', "%" . $value  . "%");
                    } else {
                        $query->orWhere($column, 'LIKE', "%" . $value  . "%");
                    }
                }
            })
            ->select('companies.*');
    }

    /**
     * Get the current balance.
     *
     * @return string
     */
    public function getCompanyLogoAttribute()
    {
        return $this->getMedia('company_logo')->last();
    }
}
