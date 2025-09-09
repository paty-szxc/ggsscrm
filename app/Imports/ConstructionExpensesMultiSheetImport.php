<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ConstructionExpensesMultiSheetImport implements WithMultipleSheets
{
    /**
     * Returns an array of sheet imports keyed by sheet name.
     *
     *
     * @return array
     */
    public function sheets(): array
    {
        //allowed sheet names in lowercase
        $allowedSheets = ['FEB. 2025', 'JUL. 2025', 'AUG. 2025', 'SEPT. 2025'];

        $sheetImports = [];

        foreach ($allowedSheets as $sheetName) {
            $sheetImports[$sheetName] = new ConstructionExpensesImport();
        }

        return $sheetImports;
    }
}

