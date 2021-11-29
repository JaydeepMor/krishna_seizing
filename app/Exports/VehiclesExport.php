<?php

namespace App\Exports;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VehiclesExport implements FromCollection, WithHeadings
{
    private $rows;

    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
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

    public function collection()
    {
        return $this->rows;
    }
}
