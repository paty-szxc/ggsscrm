<?php

namespace App\Imports;

use App\Models\ConstructionProjects;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ConstructionProjectImport implements ToModel, WithStartRow, WithMultipleSheets
{
    public function model(array $row){
        //debug: Log the row data
        Log::info('Processing row:', $row);

        //check if essential columns are empty
        if($this->isEmptyRow($row)){
            Log::info('Skipping empty row');
            return null;
        }

        $construction = new ConstructionProjects([
            'user_id' => Auth::id(),
            'date_started' => $this->convertDate($row[0] ?? null),
            'location' => $row[1] ?? null,
            'particulars' => $row[2] ?? null,
            'processed_by' => $row[3] ?? null,
            'start_process' => $this->convertCheckbox($row[4] ?? null),
            'end_process' => $this->convertCheckbox($row[5] ?? null),
            'start_actual' => $this->convertCheckbox($row[6] ?? null),
            'end_actual' => $this->convertCheckbox($row[7] ?? null),
            'date_completed' => $this->convertDate($row[8] ?? null),
            'contact_person' => $row[9] ?? null,
            'contact_no' => $row[10] ?? null,
            'remarks' => $row[11] ?? null,
        ]);

        Log::info('Created construction record:', $construction->toArray());
        return $construction;
    }

    public function startRow(): int{
        return 2; //skip the header row, start from row 2 (Excel rows are 1-based)
    }

    public function sheets(): array{
        return [
            0 => $this //0 is the index of the first sheet
        ];
    }

    /**
     *check if a row is empty by verifying essential columns
     */
    private function isEmptyRow(array $row): bool{
        //check if the first few required columns are empty
        $essentialColumns = [0, 1, 2]; //date_started, location, particulars
        
        foreach($essentialColumns as $col) {
            if (!empty($row[$col]) && trim($row[$col]) !== '') {
                return false;
            }
        }
        return true;
    }

    private function convertDate($date){
        if(empty($date) || trim($date) === ''){
            return null;
        }

        //if the date is a numeric value, treat it as an Excel serial date
        if(is_numeric($date)){
            $unixDate = ($date - 25569) * 86400;
            return gmdate('Y-m-d', $unixDate);
        }

        //try different date formats
        $formats = [
            'F j, Y',   //January 1, 2023
            'Y-m-d',    //2023-01-01
            'm/d/Y',    //01/01/2023
            'd-m-Y',    //01-01-2023
            'd/m/Y',    //01/01/2023
            'Y/m/d',    //2023/01/01
        ];

        foreach($formats as $format){
            $dateTime = \DateTime::createFromFormat($format, $date);
            if($dateTime){
                return $dateTime->format('Y-m-d');
            }
        }

        //fallback: try strtotime
        $timestamp = strtotime($date);
        if($timestamp){
            return date('Y-m-d', $timestamp);
        }
        return null;
    }

    private function convertCheckbox($value){
        if(empty($value) || trim($value) === ''){
            return 0;
        }
        $value = strtolower(trim($value));
        return in_array($value, ['1', 'true', 'yes', '✔', 'check', 'checked', 'x', 'on']) ? 1 : 0;
    }
}