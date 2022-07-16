<?php

namespace App\Console\Commands;

use App\Vehicle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;
use Cache;
use DB;

class redisCachePaginationVehicle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:redis:cache:pagination:vehicles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push vehicles to redis pagination cache.';

    private $redis;

    private $currentPage = 1;

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
        $redisKey         = Vehicle::VEHICLE_REDIS_KEY_SINGLE;

        $paginationKey    = Vehicle::VEHICLE_REDIS_PAGINATION_KEY;

        // Get all keys.
        $redisVehicleKeys = collect($this->redis->keys($redisKey . '*'));

        // First delete old pagination keys.
        $existingKeys = $this->redis->keys($paginationKey . '*');
        if (count($existingKeys) > 0) {
            $this->redis->del($existingKeys);
        }

        // Store pagination.
        if (!empty($redisVehicleKeys) && !$redisVehicleKeys->isEmpty()) {
            $currentPage    = $this->currentPage;

            $chunkSize      = Vehicle::API_PAGINATION;

            $vechicleChunks = $redisVehicleKeys->chunk($chunkSize);

            foreach ($vechicleChunks as $vechicleChunk) {
                $this->redis->set($paginationKey . $currentPage . ":" . $chunkSize, $vechicleChunk);

                $currentPage = ($currentPage + 1);
            }
        }

        return null;
    }
}
