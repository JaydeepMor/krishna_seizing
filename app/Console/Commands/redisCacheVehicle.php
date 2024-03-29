<?php

namespace App\Console\Commands;

use App\Vehicle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use DB;

class redisCacheVehicle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:cache:vehicles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push vehicles to redis cache.';

    private $redis;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->redis = Redis::connection();

        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $keyPrefix = Vehicle::VEHICLE_REDIS_KEY_SINGLE;

        $chunkSize = Vehicle::API_PAGINATION;

        // Remove old records.
        $existingKeys = $this->redis->keys($keyPrefix . '*');
        if (count($existingKeys) > 0) {
            $this->redis->del($existingKeys);
        }

        $vehiclesArray = [];

        Vehicle::
            select(['id', 'loan_number', 'customer_name', 'model', DB::raw("REGEXP_REPLACE(`registration_number`, '[^[:alnum:]]+', '') as registration_number"), 'chassis_number', 'engine_number', 'arm_rrm', 'mobile_number', 'brm', 'final_confirmation', 'final_manager_name', 'final_manager_mobile_number', 'address', 'branch', 'bkt', 'area', 'region', 'is_confirm', 'is_cancel', 'lot_number', 'finance_company_id', 'created_at as installed_date'])
            ->whereNotNull('registration_number')->where('registration_number', '!=', '')
            ->chunk($chunkSize, function($vehicles) use(&$vehiclesArray) {
                foreach ($vehicles as $vehicle) {
                    $vehicle->registration_number = reArrengeRegistrationNumber($vehicle->registration_number);

                    $vehiclesArray[$vehicle->registration_number] = $vehicle;
                }

                usleep(500000);
            });

        if (!empty($vehiclesArray)) {
            $increment = 0;
            $slab      = 5000;

            foreach ($vehiclesArray as $vehicle) {
                if (!empty($vehicle)) {
                    $redisKey = $keyPrefix . $vehicle->finance_company_id . ":" . $vehicle->registration_number;

                    $this->redis->set($redisKey, $vehicle);

                    $increment++;
                }

                if ($increment === $slab) {
                    $increment = 0;

                    usleep(500000);
                }
            }
        }

        $this->call("daily:redis:cache:pagination:vehicles");

        Vehicle::setCount();

        return null;
    }
}
