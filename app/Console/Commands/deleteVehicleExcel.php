<?php

namespace App\Console\Commands;

use App\Vehicle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class deleteVehicleExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monthly:delete_vehicle_excle';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete uploaded vehicle excels for past month.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $modal = new Vehicle();

        $files = Storage::disk($modal->fileSystem)->listContents($modal->excelPath);

        if (!empty($files)) {
            $lastDayOfPreviousMonth = Carbon::now()->startofMonth()->subMonth()->endOfMonth()->timestamp;

            foreach ($files as $file) {
                if (empty($file['path']) || empty($file['timestamp']) || $file['timestamp'] <= 0) {
                    continue;
                }

                $exists = Storage::disk($modal->fileSystem)->has($file['path']);

                if ($exists && $file['timestamp'] <= $lastDayOfPreviousMonth) {
                    Storage::disk($modal->fileSystem)->delete($file['path']);
                }
            }
        }

        return null;
    }
}
