<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Constant;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Notification;
use App\Notifications\VehicleImportComplete;

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

        Queue::before(function (JobProcessing $event) {
            // $event->connectionName
            // $event->job
            // $event->job->payload()
        });

        Queue::after(function (JobProcessed $event) {
            switch ($event->job->resolveName()) {
                case "Maatwebsite\Excel\Jobs\AfterImportJob":
                    $financeCompanyId = null;

                    try {
                        $payload          = $event->job->payload();
                        $job              =  unserialize($payload['data']['command']);
                        $financeCompanyId = objectToArray(objectToArray($job, false)['import'], false)['financeCompanyId'];
                    } catch (Exception $e) {}

                    if (!empty($financeCompanyId)) {
                        Notification::route('mail', env('VEHICLE_IMPORTED_NOTIFICATION_EMAIL', 'it.jaydeep.mor@gmail.com'))->notify(new VehicleImportComplete($financeCompanyId));
                    }
                default:
                    break;
            }
        });
    }
}
