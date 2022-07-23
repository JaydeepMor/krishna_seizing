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

        Vehicle::chunk($chunkSize, function($vehicles) use($keyPrefix) {
            foreach ($vehicles as $vehicle) {
                $vehicle->registration_number = reArrengeRegistrationNumber($vehicle->registration_number);

                $this->redis->set($keyPrefix . $vehicle->finance_company_id . ":" . $vehicle->id, $vehicle);
            }

            sleep(1);
        });

        // \Artisan::call("daily:redis:cache:pagination:vehicles");

        return null;
    }
}
