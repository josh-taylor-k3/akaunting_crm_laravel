<?php

namespace App\Jobs\Install;

use App\Abstracts\Job;
use App\Utilities\Console;

class EnableModule extends Job
{
    protected $alias;

    protected $company_id;

    protected $locale;

    /**
     * Create a new job instance.
     *
     * @param  $alias
     * @param  $company_id
     * @param  $locale
     */
    public function __construct($alias, $company_id = null, $locale = null)
    {
        $this->alias = $alias;
        $this->company_id = $company_id ?: session('company_id');
        $this->locale = $locale ?: app()->getLocale();
    }

    /**
     * Execute the job.
     *
     * @return string
     */
    public function handle()
    {
        $command = "module:enable {$this->alias} {$this->company_id} {$this->locale}";

        $result = Console::run($command);

        if ($result !== true) {
            throw new \Exception($result);
        }
    }
}
