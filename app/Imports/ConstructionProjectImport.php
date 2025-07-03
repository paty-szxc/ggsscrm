<?php

// app/Imports/ConstructionProjectImport.php

namespace App\Imports;

use App\Models\ConstructionProjects;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ConstructionProjectImport implements ToModel, WithHeadingRow
{
    public function model(array $row){
        // Debug: Log the row data
        Log::info('Processing row:', $row);
        
        Log::info('Row keys:', array_keys($row));
        Log::info('Row values:', $row);

        if(empty(array_filter($row))){
            Log::info('Skipping empty row');
            return null;
        }

        //skip rows that are actually header rows repeated in the data
        if(
            (isset($row['client']) && strtoupper(trim($row['client'])) === 'CLIENT') ||
            (isset($row['location']) && strtoupper(trim($row['location'])) === 'LOCATION') ||
            (isset($row[3]) && strtoupper(trim($row[3])) === 'CLIENT') ||
            (isset($row[4]) && strtoupper(trim($row[4])) === 'LOCATION')
        ){
            Log::info('Skipping header row in data (by key check)');
            return null;
        }

        //skip if the entire row matches the header row (case-insensitive)
        $headerRow = ['date_started', 'date_completed', 'client', 'location', 'type_of_plan_survey', 'duration', 'remarks'];
        if(array_map('strtolower', array_values($row)) === array_map('strtolower', $headerRow)){
            Log::info('Skipping header row in data (full row match)');
            return null;
        }

        $dateStarted = $this->convertDate($row[1] ?? null);
        $dateCompleted = $this->convertDate($row[2] ?? null);

        $survey = new ConstructionProjects([
            'user_id' => Auth::id(),
            'date_started' => $dateStarted,
            'date_completed' => $dateCompleted,
            'client' => $row[3] ?? null,
            'location' => $row[4] ?? null,
            'type_of_plan_survey' => $row[5] ?? null,
            'duration' => $this->calculateDuration($dateStarted, $dateCompleted),
            'remarks' => $row[7] ?? null,
        ]);

        Log::info('Created survey record:', $survey->toArray());
        return $survey;
    }

    public function startRow(): int{
        return 5; //skip the header row, start from row 4
    }

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

    private function calculateDuration($start, $end) {
        if ($start && $end) {
            $startDate = new \DateTime($start);
            $endDate = new \DateTime($end);
            return $startDate->diff($endDate)->days;
        }
        return null;
    }
}