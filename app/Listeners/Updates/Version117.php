<?php

namespace App\Listeners\Updates;

use App\Events\UpdateFinished;
use App\Models\Auth\Role;
use App\Models\Auth\Permission;
use MediaUploader;
use Storage;
use Artisan;

class Version117 extends Listener
{
    const ALIAS = 'core';

    const VERSION = '1.1.7';

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

        // Create permission
        $permission = Permission::firstOrCreate([
            'name' => 'delete-common-uploads',
            'display_name' => 'Delete Common Uploads',
            'description' => 'Delete Common Uploads',
        ]);

        // Attach permission to roles
        $roles = Role::all();

        foreach ($roles as $role) {
            $allowed = ['admin'];

            if (!in_array($role->name, $allowed)) {
                continue;
            }

            $role->attachPermission($permission);
        }

        $data = [];

        $migrations = [
            '\App\Models\Auth\User'             => 'picture',
            '\App\Models\Item\Item'             => 'picture',
            '\App\Models\Expense\Bill'          => 'attachment',
            '\App\Models\Expense\BillPayment'   => 'attachment',
            '\App\Models\Expense\Payment'       => 'attachment',
            '\App\Models\Income\Invoice'        => 'attachment',
            '\App\Models\Income\InvoicePayment' => 'attachment',
            '\App\Models\Income\Revenue'        => 'attachment',
        ];

        foreach ($migrations as $model => $name) {
            if ($model != '\App\Models\Auth\User') {
                $items = $model::where('company_id', '<>', '0')->get();
            } else {
                $items = $model::all();
            }

            $data[basename($model)] = $items;
        }

        // Clear cache after update
        Artisan::call('cache:clear');

        // Update database
        Artisan::call('migrate', ['--force' => true]);

        foreach ($migrations as $model => $name) {
            $items = $data[basename($model)];

            foreach ($items as $item) {
                if ($item->$name) {
                    $path = explode('uploads/', $item->$name);

                    $path = end($path);

                    if (!empty($item->company_id) && (strpos($path, $item->company_id . '/') === false)) {
                        $path = $item->company_id . '/' . $path;
                    }

                    if (!empty($path) && Storage::exists($path)) {
                        $media = MediaUploader::importPath(config('mediable.default_disk'), $path);

                        $item->attachMedia($media, $name);
                    }
                }
            }
        }

        $settings['company_logo'] = \App\Models\Setting\Setting::where('key', '=', 'general.company_logo')->where('company_id', '<>', '0')->get();
        $settings['invoice_logo'] = \App\Models\Setting\Setting::where('key', '=', 'general.invoice_logo')->where('company_id', '<>', '0')->get();

        foreach ($settings as $name => $items) {
            foreach ($items as $item) {
                if ($item->value) {
                    $path = explode('uploads/', $item->value);

                    $path = end($path);

                    if (!empty($item->company_id) && (strpos($path, $item->company_id . '/') === false)) {
                        $path = $item->company_id . '/' . $path;
                    }

                    if (!empty($path) && Storage::exists($path)) {
                        $company = \App\Models\Company\Company::find($item->company_id);

                        $media = \App\Models\Common\Media::where('filename', '=',  pathinfo(basename($path), PATHINFO_FILENAME))->first();

                        if ($company && !$media) {
                            $media = MediaUploader::importPath(config('mediable.default_disk'), $path);

                            $company->attachMedia($media, $name);

                            $item->update(['value' => $media->id]);
                        } elseif ($media) {
                            $item->update(['value' => $media->id]);
                        } else {
                            $item->update(['value' => '']);
                        }
                    } else {
                        $item->update(['value' => '']);
                    }
                }
            }
        }
    }
}
