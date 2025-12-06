<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ExcelImport implements ToArray, WithStartRow
{
    /**
     * Start reading from row 2 (skip header)
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
     * Convert to array
     */
    public function array(array $array)
    {
        return $array;
    }
}
