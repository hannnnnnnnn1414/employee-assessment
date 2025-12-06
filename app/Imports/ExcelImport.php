<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ExcelImport implements ToArray
{
    public function array(array $array)
    {
        return $array;
    }
}
