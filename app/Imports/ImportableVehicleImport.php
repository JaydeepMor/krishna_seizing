<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\Importable;

class ImportableVehicleImport implements ToCollection
{
    use Importable;

    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
    }
}
