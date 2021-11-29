<?php

use App\User;
use Illuminate\Database\Seeder;

class SuperadminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $confirmed = $this->command->confirm(__('Are you sure ? Because script will remove the old Superadmin credentials data and then add new.'));

        if ($confirmed) {
            $oldSuperadmins = User::where('is_admin', User::IS_ADMIN)->get();

            if (!empty($oldSuperadmins) && !$oldSuperadmins->isEmpty()) {
                User::where('is_admin', User::IS_ADMIN)->delete();
            }

            User::create([
                'id'            => User::ADMIN_ID,
                'name'          => 'Superadmin',
                'email'         => 'vrboricha@gmail.com',
                'password'      => Hash::make(env('ADMIN_PASSWORD')),
                'is_admin'      => User::IS_ADMIN,
                'imei_number'   => ""
            ]);
        }
    }
}
