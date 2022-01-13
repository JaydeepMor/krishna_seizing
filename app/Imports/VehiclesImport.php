<?php

namespace App\Imports;

use App\Vehicle;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Notification;
use App\Notifications\VehicleImportFailed;

class VehiclesImport implements ToModel, WithStartRow, WithChunkReading, ShouldQueue
{
    private $lotNumber;

    private $financeCompanyId;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 300;

    public $tries   = 5;

    public function __construct(int $lotNumber, int $financeCompanyId)
    {
        $this->lotNumber        = $lotNumber;

        $this->financeCompanyId = $financeCompanyId;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    public function chunkSize(): int
    {
        return 300;
    }

    public function batchSize(): int
    {
        return 300;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        for ($i = 0; $i <= 16; $i++) {
            $row[$i] = !isset($row[$i]) ? null : $row[$i];
        }

        return new Vehicle([
            'loan_number'                 => trim((string)$row[0]),
            'customer_name'               => trim((string)$row[1]),
            'model'                       => trim((string)$row[2]),
            'registration_number'         => trim((string)$row[3]),
            'chassis_number'              => trim((string)$row[4]),
            'engine_number'               => trim((string)$row[5]),
            'arm_rrm'                     => trim((string)$row[6]),
            'mobile_number'               => trim((string)$row[7]),
            'brm'                         => trim((string)$row[8]),
            'final_confirmation'          => trim((string)$row[9]),
            'final_manager_name'          => trim((string)$row[10]),
            'final_manager_mobile_number' => trim((string)$row[11]),
            'address'                     => trim((string)$row[12]),
            'branch'                      => trim((string)$row[13]),
            'bkt'                         => trim((string)$row[14]),
            'area'                        => trim((string)$row[15]),
            'region'                      => trim((string)$row[16]),
            'lot_number'                  => trim($this->lotNumber),
            'finance_company_id'          => trim($this->financeCompanyId)
        ]);
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(\Exception $exception)
    {
        Notification::route('mail', env('EXCEPTION_EMAILS', 'it.jaydeep.mor@gmail.com'))->notify(new VehicleImportFailed($this->financeCompanyId, $exception->getMessage()));
    }
}
