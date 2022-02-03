<?php

namespace App\Console\Commands;

use App\Vehicle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class redisVehicle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily:redis_vehicle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push vehicles to Redis cache.';

    private $perPage        = 25000;

    private $pageNumber     = 1;

    private $dataPageNumber = 1;

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
        // Get vehicle count.
        $count = Vehicle::whereNotNull('registration_number')->where('registration_number', '!=', '')->count();

        $loop  = (int)ceil($count / $this->perPage);

        // Remove old records.
        $existingKeys = Redis::keys(Vehicle::VEHICLE_REDIS_KEY . '*');
        if (count($existingKeys) > 0) {
            Redis::del($existingKeys);
        }

        if ($loop > 0) {
            for ($pageNo = $this->pageNumber; $pageNo <= $loop; $pageNo++) {
                $this->pageNumber = $pageNo;

                // Store to Redis cache.
                $this->chunkAndStoreToRedis($count);
            }
        }

        return null;
    }

    private function getVechicle()
    {
        $perPage = $this->perPage;

        $pageNo  = $this->pageNumber;

        $vehicles = Vehicle::select(['id', 'loan_number', 'customer_name', 'model', 'registration_number', 'chassis_number', 'engine_number', 'arm_rrm', 'mobile_number', 'brm', 'final_confirmation', 'final_manager_name', 'final_manager_mobile_number', 'address', 'branch', 'bkt', 'area', 'region', 'is_confirm', 'is_cancel', 'lot_number', 'finance_company_id'])->whereNotNull('registration_number')->where('registration_number', '!=', '')->paginate($perPage, ['*'], 'page', $pageNo);

        return $vehicles;
    }

    private function chunkAndStoreToRedis($total)
    {
        $modal     = new Vehicle();

        $redis     = $this->redis;

        $chunkSize = Vehicle::API_PAGINATION;

        $lastPage  = (int)ceil($total / $chunkSize);

        // Get all vehicles by limit offset.
        $vehicles  = $this->getVechicle();

        if (!empty($vehicles) && !$vehicles->isEmpty()) {
            $this->dataPageNumber = $this->dataPageNumber;

            foreach ($vehicles->chunk($chunkSize) as $vehicle) {
                $redisData = collect();

                foreach ($vehicle as $key => $row) {
                    $vehicle[$key]['registration_number'] = reArrengeRegistrationNumber($vehicle[$key]['registration_number']);

                    // unset($vehicle[$key]['finance_company_id']);
                }

                $redisData->put('current_page', $this->dataPageNumber);
                $redisData->put('last_page', $lastPage);
                $redisData->put('per_page', $chunkSize);
                $redisData->put('total', $total);
                $redisData->put('current_page_total', $vehicle->count());
                $redisData->put('data', $vehicle);

                $redis->set($modal::VEHICLE_REDIS_KEY . $this->dataPageNumber . ":" . $chunkSize, $redisData);

                $this->dataPageNumber++;
            }
        }

        return null;
    }
}
