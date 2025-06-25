<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class VouchersMultiSheetImport implements WithMultipleSheets
{
    /**
     * Returns an array of sheet imports keyed by sheet name.
     *
     * Only sheets named 'january', 'feb', 'march' (case-insensitive) will be imported.
     *
     * @return array
     */
    public function sheets(): array
    {
        // Allowed sheet names in lowercase
        $allowedSheets = ['JAN 2025', 'FEB 2025', 'MAR 2025', 'APR 2025'];

        $sheetImports = [];

        foreach ($allowedSheets as $sheetName) {
            $sheetImports[$sheetName] = new VoucherImport();
        }

        return $sheetImports;
    }
}

