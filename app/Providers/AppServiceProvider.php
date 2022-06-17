<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Constant;
use App\User;
use App\Vehicle;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Notification;
use App\Notifications\VehicleImportComplete;
use App\Notifications\VehicleExportComplete;
use Illuminate\Support\Facades\Artisan;
use Cache;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        date_default_timezone_set('UTC');

        // Load default constants.
        $constants = Constant::all();

        $userModel = new User();

        $vehicleModel = new Vehicle();

        $cacheKey     = Vehicle::VEHICLE_COUNT_CACHE_KEY;
        $cacheMinutes = Vehicle::VEHICLE_COUNT_CACHE_MINUTES;

        if (!empty($constants) && !$constants->isEmpty()) {
            foreach ($constants as $constant) {
                if (empty($constant->key) || empty($constant->value)) {
                    continue;
                }

                if (!defined(strtoupper($constant->key))) {
                    define(strtoupper(trim($constant->key)), $constant->value);
                }
            }
        }

        $importEmail = config('mail.imported.email', 'it.jaydeep.mor@gmail.com');
        $exportEmail = config('mail.exported.email', 'it.jaydeep.mor@gmail.com');

        Queue::before(function (JobProcessing $event) {
            // $event->connectionName
            // $event->job
            // $event->job->payload()
        });

        Queue::after(function (JobProcessed $event) use($importEmail, $exportEmail, $userModel, $vehicleModel, $cacheKey, $cacheMinutes) {
            switch ($event->job->resolveName()) {
                case "Maatwebsite\Excel\Jobs\AfterImportJob":
                    $financeCompanyId = null;

                    try {
                        $payload          = $event->job->payload();
                        $job              = unserialize($payload['data']['command']);
                        $financeCompanyId = objectToArray(objectToArray($job, false)['import'], false)['financeCompanyId'];
                    } catch (Exception $e) {}

                    if (!empty($financeCompanyId)) {
                        // Run vehicle Redis cache.
                        // Artisan::call("daily:redis_vehicle");

                        $userModel::isDownloadableForAll();

                        // Forget count cache.
                        Cache::forget($cacheKey);

                        // Add new count cache.
                        $count = Vehicle::whereNotNull('registration_number')->where('registration_number', '!=', '')->count();
                        Cache::put($cacheKey, $count, $cacheMinutes);

                        Notification::route('mail', $importEmail)->notify(new VehicleImportComplete($financeCompanyId));
                    }

                    break;
                case "Maatwebsite\Excel\Jobs\StoreQueuedExport":
                    try {
                        $payload  = $event->job->payload();
                        $job      = unserialize($payload['data']['command']);
                        $filePath = objectToArray($job, false)['filePath'];

                        Notification::route('mail', $exportEmail)->notify(new VehicleExportComplete($filePath));
                    } catch (Exception $e) {}

                    break;
                default:
                    break;
            }
        });
    }
}
