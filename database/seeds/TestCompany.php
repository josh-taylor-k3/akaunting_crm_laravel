<?php

namespace Database\Seeds;

use App\Abstracts\Model;
use App\Models\Auth\User;
use App\Models\Common\Company;
use Date;
use Illuminate\Database\Seeder;

class TestCompany extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->call(Roles::class);

        $this->createCompany();

        $this->createUser();

        Model::reguard();
    }

    private function createCompany()
    {
        $rows = [
            [
                'id' => '1',
                'domain' => 'test.com',
            ],
        ];

        foreach ($rows as $row) {
            Company::create($row);
        }

        setting()->setExtraColumns(['company_id' => '1']);
        setting()->set([
            'company.name'                      => 'Test Inc.',
            'company.email'                     => 'info@test.com',
            'company.address'                   => 'New Street 1254',
            'localisation.financial_start'      => '01-01',
            'default.currency'                  => 'USD',
            'default.account'                   => '1',
            'default.payment_method'            => 'offline-paymentz.cash.1',
            'schedule.bill_days'                => '10,5,3,1',
            'schedule.invoice_days'             => '1,3,5,10',
            'schedule.send_invoice_reminder'    => true,
            'schedule.send_bill_reminder'       => true,
        ]);
        setting()->save();

        $this->command->info('Test company created.');
    }

    public function createUser()
    {
        // Create user
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@akaunting.com',
            'password' => '123456',
            'last_logged_in_at' => Date::now(),
        ]);

        // Attach Role
        $user->roles()->attach(1);

        // Attach company
        $user->companies()->attach(1);

        $this->command->info('Admin user created.');
    }
}
