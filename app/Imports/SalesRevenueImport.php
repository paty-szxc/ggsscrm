<?php

namespace App\Imports;

use App\Models\SalesRevenue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class SalesRevenueImport implements ToCollection
{
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows){
        foreach($rows as $row){
            //skip empty rows
            if ($this->isEmptyRow($row)) {
                continue;
            }

            $dateOfSurvey = $this->convertDate($row[0] ?? null);
            $firstDateOfCollection = $this->convertDate($row[6] ?? null);
            $secondDateOfCollection = $this->convertDate($row[8] ?? null);
            $thirdDateOfCollection = $this->convertDate($row[10] ?? null);
            $fourthDateOfCollection = $this->convertDate($row[12] ?? null);
            $remarks = $row[17] ?? null;
            $fullyPaidDate = $this->extractFullyPaidDate($remarks);

            SalesRevenue::create([
                'date_of_survey' => $dateOfSurvey,
                'location' => $row[1] ?? null,
                'type_of_survey' => $row[2] ?? null,
                'expenses' => $this->cleanNumber($row[3] ?? null),
                'receipt_no' => $row[4] ?? null,
                'project_cost' => $this->cleanNumber($row[5] ?? null),
                'first_date_of_collection' => $firstDateOfCollection,
                'first_collection' => $this->cleanNumber($row[7] ?? null),
                'second_date_of_collection' => $secondDateOfCollection,
                'second_collection' => $this->cleanNumber($row[9] ?? null),
                'third_date_of_collection' => $thirdDateOfCollection,
                'third_collection' => $this->cleanNumber($row[11] ?? null),
                'fourth_date_of_collection' => $fourthDateOfCollection,
                'fourth_collection' => $this->cleanNumber($row[13] ?? null),
                'total' => $this->cleanNumber($row[14] ?? null),
                'withholding_tax' => $this->cleanWithholdingTax($row[16] ?? null),
                // 'remarks' => $row[17] ?? null,
                'fully_paid_date' => $fullyPaidDate,
            ]);
        }
    }


    private function extractFullyPaidDate($remarks) {
        if (preg_match('/FULLY PAID\s+(\d{1,2}\/\d{1,2}\/\d{4})/i', $remarks, $matches)) {
            return $this->convertDate($matches[1]);
        }
        return null;
    }
    /**
     * Check if a row is empty
     */
    private function isEmptyRow($row){
        // Convert to array if it's a Collection
        $rowArray = $row instanceof Collection ? $row->toArray() : $row;
        
        return empty(array_filter($rowArray, function ($value){
            return $value !== null && $value !== '';
        }));
    }

    private function cleanNumber($value){
        if(is_null($value) || $value === ''){
            return null;
        }
        
        // Remove thousands separators and convert to float
        return (float) str_replace(',', '', trim($value));
    }

    private function convertDate($date){
        if(empty($date)){
            return null;
        }

        // Try different date formats
        $formats = ['F j, Y', 'Y-m-d', 'm/d/Y', 'd/m/Y'];
        
        foreach($formats as $format){
            $dateTime = \DateTime::createFromFormat($format, $date);
            if($dateTime !== false){
                return $dateTime->format('Y-m-d');
            }
        }
        
        return null;
    }
    // private function cleanWithholdingTax($value){
    //     if (empty($value)) {
    //         return null;
    //     }

    //     // If already in "w/ X% TAX" format, keep as is
    //     if (preg_match('/w\/\s*\d+%?\s*TAX/i', $value)) {
    //         return strtoupper($value); // Standardize case to "w/ X% TAX"
    //     }

    //     // If value contains percentage (e.g., "2%", "5% tax")
    //     if (preg_match('/(\d+%?)/i', $value, $matches)) {
    //         $percentage = rtrim($matches[1], '%'); // Remove % if exists
    //         return "w/ {$percentage}% TAX";
    //     }

    //     // For any other numeric value
    //     if (is_numeric($value)) {
    //         return "w/ {$value}% TAX";
    //     }

    //     return null;
    // }
    private function cleanWithholdingTax($value) {
        if (empty($value)) {
            return null;
        }

        // Standardize the input by removing spaces after "w/" and making consistent
        $value = preg_replace('/w\/\s*/i', 'w/', trim($value));
        
        // Check if already in correct format (case insensitive)
        if (preg_match('/^w\/\d+%?\s*TAX$/i', $value)) {
            // Extract the percentage number
            preg_match('/\d+/', $value, $matches);
            $percentage = $matches[0] ?? 0;
            return "w/ {$percentage}% TAX"; // Return in standardized format
        }

        // If value contains just a percentage number
        if (preg_match('/^\d+%?$/', trim($value))) {
            $percentage = rtrim(trim($value), '%');
            return "w/ {$percentage}% TAX";
        }

        return null; // Return null for any format we don't recognize
    }
}