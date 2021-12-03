<?php

use Illuminate\Database\Seeder;
use App\Vehicle;

class VehiclesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env('APP_ENV', '') != 'local') {
            $this->command->error(__("This seeder only works in local environments. Because live has some important data so."), 'error');

            return false;
        }

        $confirmed = $this->command->confirm(__('Are you sure ? Because script will remove all the old Vehicles and then add new.'));

        if ($confirmed) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Vehicle::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $faker = Faker\Factory::create();

            for ($i = 0; $i <= 10000; $i++) {
                $isConfirm = (string)random_int(0, 1);
                $isCancel  = ($isConfirm == "1") ? "0" : "1";

                Vehicle::create([
                    'loan_number'                   => Str::random(10),
                    'customer_name'                 => $faker->name,
                    'model'                         => Str::random(10),
                    'registration_number'           => Str::random(10),
                    'chassis_number'                => Str::random(10),
                    'engine_number'                 => Str::random(10),
                    'arm_rrm'                       => Str::random(10),
                    'mobile_number'                 => $faker->phoneNumber,
                    'brm'                           => Str::random(10),
                    'final_confirmation'            => Str::random(10),
                    'final_manager_name'            => Str::random(10),
                    'final_manager_mobile_number'   => $faker->phoneNumber,
                    'address'                       => Str::random(10),
                    'branch'                        => Str::random(10),
                    'bkt'                           => Str::random(10),
                    'area'                          => Str::random(10),
                    'region'                        => Str::random(10),
                    'is_confirm'                    => $isConfirm,
                    'is_cancel'                     => $isCancel,
                    'user_id'                       => NULL,
                    'lot_number'                    => "1"
                ]);
            }
        }
    }
}
