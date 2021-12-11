<?php

use Illuminate\Database\Seeder;
use App\Constant;
use App\Vehicle;

class ConstantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $confirmed = $this->command->confirm(__('Are you sure ? Because script will remove all the old Constants and then add new.'));

        if ($confirmed) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Constant::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $vehicle = new Vehicle();

            Constant::create([
                'key'   => 'USER_APP_RESTORE_CYCLE_DAYS',
                'value' => '30'
            ]);

            Constant::create([
                'key'   => 'EXCEL_DATABASE_FIELDS',
                'value' => json_encode($vehicle->getFillable())
            ]);

            Constant::create([
                'key'   => 'EXCEL_DATABASE_DEFAULT_FIELDS',
                'value' => json_encode(["customer_name","model","registration_number","chassis_number","engine_number"])
            ]);

            Constant::create([
                'key'   => 'DEFAULT_DATE_FORMAT',
                'value' => 'Y-m-d'
            ]);

            Constant::create([
                'key'   => 'DEFAULT_DATE_TIME_FORMAT',
                'value' => 'Y-m-d HH:ii:ss'
            ]);

            Constant::create([
                'key'   => 'RELEASED_APPLICATION',
                'value' => ''
            ]);
        }
    }
}
