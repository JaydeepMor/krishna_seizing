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

    public function __construct(int $lotNumber)
    {
        $this->lotNumber = $lotNumber;
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
        return 20000;
    }

    public function batchSize(): int
    {
        return 20000;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Vehicle([
            'loan_number'                 => $row[0],
            'customer_name'               => $row[1],
            'model'                       => $row[2],
            'registration_number'         => $row[3],
            'chassis_number'              => $row[4],
            'engine_number'               => $row[5],
            'arm_rrm'                     => $row[6],
            'mobile_number'               => $row[7],
            'brm'                         => $row[8],
            'final_confirmation'          => $row[9],
            'final_manager_name'          => $row[10],
            'final_manager_mobile_number' => $row[11],
            'address'                     => $row[12],
            'branch'                      => $row[13],
            'bkt'                         => $row[14],
            'area'                        => $row[15],
            'region'                      => $row[16],
            'lot_number'                  => $this->lotNumber
        ]);
    }
}
