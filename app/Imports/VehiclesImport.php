<?php

namespace App\Imports;

use App\Vehicle;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class VehiclesImport implements ToModel, WithStartRow, WithChunkReading, ShouldQueue
{
    private $lotNumber;

    private $financeCompanyId;

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
        return 500;
    }

    public function batchSize(): int
    {
        return 500;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Vehicle([
            'loan_number'                 => (string)$row[0],
            'customer_name'               => (string)$row[1],
            'model'                       => (string)$row[2],
            'registration_number'         => (string)$row[3],
            'chassis_number'              => (string)$row[4],
            'engine_number'               => (string)$row[5],
            'arm_rrm'                     => (string)$row[6],
            'mobile_number'               => (string)$row[7],
            'brm'                         => (string)$row[8],
            'final_confirmation'          => (string)$row[9],
            'final_manager_name'          => (string)$row[10],
            'final_manager_mobile_number' => (string)$row[11],
            'address'                     => (string)$row[12],
            'branch'                      => (string)$row[13],
            'bkt'                         => (string)$row[14],
            'area'                        => (string)$row[15],
            'region'                      => (string)$row[16],
            'lot_number'                  => $this->lotNumber,
            'finance_company_id'          => $this->financeCompanyId
        ]);
    }
}
