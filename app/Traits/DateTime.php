<?php

namespace App\Traits;

use Date;
use DateTimeZone;
use Illuminate\Support\Str;
use Carbon\CarbonPeriod;
use App\Traits\SearchString;

trait DateTime
{
    use SearchString;

    /*
     * Get the date format based on company settings.
     * getDateFormat method is used by Eloquent
     *
     * @return string
     */
    public function getCompanyDateFormat()
    {
        $default = 'd M Y';

        // Make sure it's installed
        if (! config('app.installed') && ! env_is_testing()) {
            return $default;
        }

        // Make user is logged in
        if (!user()) {
            return $default;
        }

        $chars = ['dash' => '-', 'slash' => '/', 'dot' => '.', 'comma' => ',', 'space' => ' '];

        $date_format = setting('localisation.date_format', $default);
        $date_separator = $chars[setting('localisation.date_separator', 'space')];

        return str_replace(' ', $date_separator, $date_format);
    }

    public function scopeMonthsOfYear($query, $field)
    {
        $now = Date::now();
        $year = $this->getSearchStringValue('year', $now->year);

        $financial_start = $this->getFinancialStart($year);

        // Check if FS has been customized
        if ($now->startOfYear()->format('Y-m-d') === $financial_start->format('Y-m-d')) {
            $start = Date::parse($year . '-01-01')->startOfDay()->format('Y-m-d H:i:s');
            $end = Date::parse($year . '-12-31')->endOfDay()->format('Y-m-d H:i:s');
        } else {
            $start = $financial_start->startOfDay()->format('Y-m-d H:i:s');
            $end = $financial_start->addYear(1)->subDays(1)->endOfDay()->format('Y-m-d H:i:s');
        }

        return $query->whereBetween($field, [$start, $end]);
    }

    public function getTimezones()
    {
        return collect(DateTimeZone::listIdentifiers())
            ->mapWithKeys(function ($timezone) {
                return [$timezone => Str::after($timezone, '/')];
            })
            ->groupBy(function ($item, $key) {
                return Str::before($key, '/');
            }, preserveKeys: true);;
    }

    public function getFinancialStart($year = null)
    {
        $now = Date::now();
        $start = Date::now()->startOfYear();

        $setting = explode('-', setting('localisation.financial_start'));

        $day = !empty($setting[0]) ? $setting[0] : $start->day;
        $month = !empty($setting[1]) ? $setting[1] : $start->month;
        $year = $year ?? $this->getSearchStringValue('year', $now->year);

        $financial_start = Date::create($year, $month, $day);

        if ((setting('localisation.financial_denote') == 'ends') && ($financial_start->dayOfYear != 1)) {
            $financial_start->subYear();
        }

        return $financial_start;
    }

    public function getFinancialYear($year = null)
    {
        $start = $this->getFinancialStart($year);

        return CarbonPeriod::create($start, $start->copy()->addYear()->subDay()->endOfDay());
    }

    public function getFinancialQuarters($year = null)
    {
        $quarters = [];
        $start = $this->getFinancialStart($year);

        for ($i = 0; $i < 4; $i++) {
            $quarters[] = CarbonPeriod::create($start->copy()->addQuarters($i), $start->copy()->addQuarters($i + 1)->subDay()->endOfDay());
        }

        return $quarters;
    }

    public function getDatePickerShortcuts()
    {
        $today = new Date();
        $financial_year = $this->getFinancialYear();
        $financial_quarters = $this->getFinancialQuarters();

        foreach ($financial_quarters as $quarter) {
            if ($today->lessThan($quarter->getStartDate()) || $today->greaterThan($quarter->getEndDate())) {
                $previous_quarter = $quarter;

                continue;
            }

            $this_quarter = $quarter;

            break;
        }

        if (!isset($this_quarter)) {
            $this_quarter = $financial_quarters[0];
        }

        if (!isset($previous_quarter)) {
            $previous_quarter = $financial_quarters[0];
        }

        $date_picker_shortcuts = [
            trans('reports.this_year') => [
                'start' => $financial_year->getStartDate()->format('Y-m-d'),
                'end' => $financial_year->getEndDate()->format('Y-m-d'),
            ],
            trans('reports.previous_year') => [
                'start' => $financial_year->getStartDate()->copy()->subYear()->format('Y-m-d'),
                'end' => $financial_year->getEndDate()->copy()->subYear()->format('Y-m-d'),
            ],
            trans('reports.this_quarter') => [
                'start' => $this_quarter->getStartDate()->format('Y-m-d'),
                'end' => $this_quarter->getEndDate()->format('Y-m-d'),
            ],
            trans('reports.previous_quarter') => [
                'start' => $previous_quarter->getStartDate()->format('Y-m-d'),
                'end' => $previous_quarter->getEndDate()->format('Y-m-d'),
            ],
            trans('reports.last_12_months') => [
                'start' => $today->copy()->subYear()->startOfDay()->format('Y-m-d'),
                'end' => $today->copy()->subDay()->endOfDay()->format('Y-m-d'),
            ],
        ];

        return $date_picker_shortcuts;
    }

    public function getMonthlyDateFormat($year = null)
    {
        $format = 'M Y';

        return $format;
    }

    public function getQuarterlyDateFormat($year = null)
    {
        $format = 'M Y';

        return $format;
    }

    public function getYearlyDateFormat()
    {
        $format = 'Y';

        return $format;
    }
}
