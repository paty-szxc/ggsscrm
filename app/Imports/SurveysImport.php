<?php

namespace App\Imports;

use App\Models\Survey;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SurveysImport implements ToModel, WithStartRow, WithMultipleSheets
{
    /**
     * @param array $row
     *
     * @return Survey|null
     */
    public function model(array $row){
        // Debug: Log the row data
        Log::info('Processing row:', $row);
        
        // Check if row has any data
        if(empty(array_filter($row))){
            Log::info('Skipping empty row');
            return null;
        }

        $dateStarted = $this->convertDate($row[1] ?? null);
        $dateApproved = $this->convertDate($row[9] ?? null);
        $dateCompleted = $this->convertDate($row[10] ?? null);

        $survey = new Survey([
            'user_id' => Auth::id(),
            'date_started' => $dateStarted,
            'date_completed' => $dateCompleted,
            'location' => $row[2] ?? null,
            'survey_details' => $row[3] ?? null,
            'area' => $row[4] ?? null,
            'processed_by' => $row[5] ?? null,
            'survey' => $this->convertCheckbox($row[6] ?? null),
            'data_process' => $this->convertCheckbox($row[7] ?? null),
            'plans' => $this->convertCheckbox($row[8] ?? null),
            'date_approved' => $dateApproved,
            'remarks' => $row[11] ?? null,
            'contact_person' => $row[12] ?? null,
            'contact_no' => $row[13] ?? null,
            'thru' => $row[14] ?? null,
        ]);

        Log::info('Created survey record:', $survey->toArray());
        return $survey;
    }

    /**
     * @return int
     */
    public function startRow(): int{
        return 3; //skip the header row, start from row 2
    }

    /**
     * @return array
     */
    public function sheets(): array{
        //return only the 'PROJECT MONITORING' sheet
        return [
            'PROJECT MONITORING' => $this,
        ];
    }

    /**
     *convert date from format 'F j, Y' to 'Y-m-d'
     */
    private function convertDate($date){
        if(empty($date)){
            return null;
        }

        //if the date is a numeric value, treat it as an Excel serial date
        if(is_numeric($date)){
            //excel's epoch starts at 1900-01-01
            $unixDate = ($date - 25569) * 86400;
            return gmdate('Y-m-d', $unixDate);
        }

        //try to parse as 'F j, Y'
        $dateTime = \DateTime::createFromFormat('F j, Y', $date);
        if($dateTime){
            return $dateTime->format('Y-m-d');
        }

        //try to parse as 'Y-m-d' (in case it's already formatted)
        $dateTime = \DateTime::createFromFormat('Y-m-d', $date);
        if($dateTime){
            return $dateTime->format('Y-m-d');
        }

        //try to parse as 'm/d/Y' (common Excel export)
        $dateTime = \DateTime::createFromFormat('m/d/Y', $date);
        if($dateTime){
            return $dateTime->format('Y-m-d');
        }

        //fallback: try strtotime
        $timestamp = strtotime($date);
        if($timestamp){
            return date('Y-m-d', $timestamp);
        }
        return null;
    }

    /**
     *convert various checkbox values to boolean (1 or 0)
     */
    private function convertCheckbox($value){
        if(empty($value)){
            return 0;
        }
        $value = strtolower(trim($value));
        return in_array($value, ['1', 'true', 'yes', '✔', 'check', 'checked', 'x', 'on']) ? 1 : 0;
    }
}