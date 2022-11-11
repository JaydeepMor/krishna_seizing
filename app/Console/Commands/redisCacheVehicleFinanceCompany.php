<?php

namespace App\Console\Commands;

use App\Vehicle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use DB;

class redisCacheVehicleFinanceCompany extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:cache:vehicles:finance:company {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push vehicles to redis cache finance company wise.';

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
        $financeCompanyId = $this->argument('id');

        if (empty($financeCompanyId)) {
            return false;
        }

        $keyPrefix = Vehicle::VEHICLE_REDIS_KEY_SINGLE;

        $chunkSize = Vehicle::API_PAGINATION;

        // Remove old records.
        $existingKeys = $this->redis->keys($keyPrefix . $financeCompanyId . '*');
        if (count($existingKeys) > 0) {
            $this->redis->del($existingKeys);
        }

        Vehicle::
            select(['id', 'loan_number', 'customer_name', 'model', DB::raw("REGEXP_REPLACE(`registration_number`, '[^[:alnum:]]+', '') as registration_number"), 'chassis_number', 'engine_number', 'arm_rrm', 'mobile_number', 'brm', 'final_confirmation', 'final_manager_name', 'final_manager_mobile_number', 'address', 'branch', 'bkt', 'area', 'region', 'is_confirm', 'is_cancel', 'lot_number', 'finance_company_id', 'created_at as installed_date'])
            ->whereNotNull('registration_number')
            ->where('registration_number', '!=', '')
            ->where('finance_company_id', (int)$financeCompanyId)
            ->chunk($chunkSize, function($vehicles) use($keyPrefix) {
                foreach ($vehicles as $vehicle) {
                    $vehicle->registration_number = reArrengeRegistrationNumber($vehicle->registration_number);

                    $redisKey                     = $keyPrefix . $vehicle->finance_company_id . ":" . $vehicle->registration_number;

                    $wildcardRedisKey             = $keyPrefix . "*:" . $vehicle->registration_number;

                    $existKeys                    = $this->redis->keys($wildcardRedisKey);

                    if (count($existKeys) > 0) {
                        $this->redis->del($existKeys);
                    }

                    $this->redis->set($redisKey, $vehicle);
                }

                usleep(500000);
            });

        $this->call("daily:redis:cache:pagination:vehicles");

        return null;
    }
}
