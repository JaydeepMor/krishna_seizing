<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class SubseizersExport implements FromCollection, WithHeadings, WithEvents
{
    private $rows;

    public function __construct(Collection $rows)
    {
        $this->rows = $rows;
    }

    public function headings(): array
    {
        return [
            'Id',
            'Name',
            'Address',
            'Email',
            'Contact Number',
            'Team Leader',
            'Reference Name',
            'IMEI Number',
            'Status',
            'Group',
            'Created At'
        ];
    }

    public function collection()
    {
        return $this->rows;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class    => function(AfterSheet $event) {
                $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(20);
                $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(30);
                $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(40);
                $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(40);
            }
        ];
    }
}
