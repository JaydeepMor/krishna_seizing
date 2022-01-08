<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use App\Notifications\VehicleExportFailed;
use Notification;
use App\Vehicle;

class VehiclesExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, ShouldQueue
{
    use Exportable;

    private $modal;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300;

    public $tries   = 5;

    public function __construct()
    {
        $this->modal = new Vehicle();
    }

    public function chunkSize(): int
    {
        return 300;
    }

    public function batchSize(): int
    {
        return 300;
    }

    public function headings(): array
    {
        return [
            'Loan Number',
            'Customer Name',
            'Model',
            'REG. NO.',
            'Chassis No',
            'Engine No',
            'ARM_RRM',
            'Mob No',
            'BRM',
            'Final Confirmation',
            'FINAL MANAGER NAME',
            'FINAL MANAGER MOB NO',
            'Add',
            'BRANCH',
            'BKT',
            'area',
            'REGION',
            'Is Confirmed',
            'Is Cancelled',
            'Created At'
        ];
    }

    public function map($vehicle): array
    {
        $modal = $this->modal;

        return [
            $vehicle->loan_number,
            $vehicle->customer_name,
            $vehicle->model,
            $vehicle->registration_number,
            $vehicle->chassis_number,
            $vehicle->engine_number,
            $vehicle->arm_rrm,
            $vehicle->mobile_number,
            $vehicle->brm,
            $vehicle->final_confirmation,
            $vehicle->final_manager_name,
            $vehicle->final_manager_mobile_number,
            $vehicle->address,
            $vehicle->branch,
            $vehicle->bkt,
            $vehicle->area,
            $vehicle->region,
            $modal->isConfirm[$vehicle->is_confirm],
            $modal->isCancel[$vehicle->is_cancel],
            date(DEFAULT_DATE_FORMAT, strtotime($vehicle->created_at))
        ];
    }

    public function query()
    {
        $modal = $this->modal;

        $query = $modal::query();

        $query = (new \App\Http\Controllers\ReportController())->filter(request(), $modal, $query);

        return $query;
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        Notification::route('mail', env('EXCEPTION_EMAILS', 'it.jaydeep.mor@gmail.com'))->notify(new VehicleExportFailed());
    }
}
