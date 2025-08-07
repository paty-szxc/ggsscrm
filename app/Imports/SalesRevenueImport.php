<?php

namespace App\Imports;

use App\Models\SalesRevenue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;

class SalesRevenueImport implements ToCollection, WithStartRow, WithMultipleSheets
{
    /**
     * @param Collection $rows
     */
    private $importedCount = 0;
    private $skippedCount = 0;

    // Add these public methods to access the counts
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }
    public function collection(Collection $rows){
        Log::info('Starting import process', ['total_rows' => $rows->count()]);

        foreach ($rows as $index => $row) {
            $rowNumber = $index + $this->startRow() + 1;
            
            try {
                Log::debug("Processing row {$rowNumber}", ['data' => $row->toArray()]);

                if ($this->isEmptyRow($row)) {
                    $this->skippedCount++;
                    Log::info("Skipping empty row {$rowNumber}");
                    continue;
                }

                if ($this->isMonthHeaderRow($row)) {
                    $this->skippedCount++;
                    Log::info("Skipping month header row {$rowNumber}");
                    continue;
                }

                $data = $this->prepareData($row);
                
                Log::debug("Prepared data for row {$rowNumber}", $data);

                $record = SalesRevenue::create($data);
                
                if ($record->exists) {
                    $this->importedCount++;
                    Log::info("Successfully imported row {$rowNumber}", ['id' => $record->id]);
                } else {
                    $this->skippedCount++;
                    Log::error("Failed to create record for row {$rowNumber}");
                }

            } catch (\Exception $e) {
                Log::error("Error processing row {$rowNumber}: " . $e->getMessage(), [
                    'row_data' => $row->toArray(),
                    'error' => $e->getTraceAsString()
                ]);
                $this->skippedCount++;
            }
        }

        Log::info("Import completed", [
            'imported' => $this->importedCount,
            'skipped' => $this->skippedCount
        ]);
        // foreach($rows as $row){
        //     //skip empty rows
        //     if ($this->isEmptyRow($row)) {
        //         continue;
        //     }

        //     $dateOfSurvey = $this->convertDate($row[0] ?? null);
        //     $firstDateOfCollection = $this->convertDate($row[6] ?? null);
        //     $secondDateOfCollection = $this->convertDate($row[8] ?? null);
        //     $thirdDateOfCollection = $this->convertDate($row[10] ?? null);
        //     $fourthDateOfCollection = $this->convertDate($row[12] ?? null);
        //     $remarks = $row[17] ?? null;
        //     $fullyPaidDate = $this->extractFullyPaidDate($remarks);

        //     SalesRevenue::create([
        //         'date_of_survey' => $dateOfSurvey,
        //         'location' => $row[1] ?? null,
        //         'type_of_survey' => $row[2] ?? null,
        //         'expenses' => $this->cleanNumber($row[3] ?? null),
        //         'receipt_no' => $row[4] ?? null,
        //         'project_cost' => $this->cleanNumber($row[5] ?? null),
        //         'first_date_of_collection' => $firstDateOfCollection,
        //         'first_collection' => $this->cleanNumber($row[7] ?? null),
        //         'second_date_of_collection' => $secondDateOfCollection,
        //         'second_collection' => $this->cleanNumber($row[9] ?? null),
        //         'third_date_of_collection' => $thirdDateOfCollection,
        //         'third_collection' => $this->cleanNumber($row[11] ?? null),
        //         'fourth_date_of_collection' => $fourthDateOfCollection,
        //         'fourth_collection' => $this->cleanNumber($row[13] ?? null),
        //         'total' => $this->cleanNumber($row[14] ?? null),
        //         'withholding_tax' => $this->cleanWithholdingTax($row[16] ?? null),
        //         // 'remarks' => $row[17] ?? null,
        //         'fully_paid_date' => $fullyPaidDate,
        //     ]);
        // }
    }

    private function prepareData($row): array{
        return [
            'date_of_survey' => $this->convertDate($row[0]),
            'location' => $row[1] ?? null,
            'type_of_survey' => $row[2] ?? null,
            'expenses' => $this->cleanNumber($row[3] ?? null),
            'receipt_no' => $row[4] ?? null,
            'project_cost' => $this->cleanNumber($row[5] ?? null),
            'first_date_of_collection' => $this->convertDate($row[6]),
            'first_collection' => $this->cleanNumber($row[7] ?? null),
            'second_date_of_collection' => $this->convertDate($row[8]),
            'second_collection' => $this->cleanNumber($row[9] ?? null),
            'third_date_of_collection' => $this->convertDate($row[10]),
            'third_collection' => $this->cleanNumber($row[11] ?? null),
            'fourth_date_of_collection' => $this->convertDate($row[12]),
            'fourth_collection' => $this->cleanNumber($row[13] ?? null),
            'total' => $this->cleanNumber($row[14] ?? null),
            'withholding_tax' => $this->cleanWithholdingTax($row[16] ?? null),
            'remarks' => $row[17] ?? null,
            'fully_paid_date' =>  $this->extractFullyPaidDate($row[17] ?? null),
        ];
    }

    public function startRow(): int{
        return 3; //skip first 4 rows (header rows)
    }

    public function sheets(): array{
        return [
            1 => $this
        ];
    }

    private function isEmptyRow($row): bool{
        //check first 3 essential columns
        return empty($row[0]) && empty($row[1]) && empty($row[2]);
    }

    private function isMonthHeaderRow($row): bool{
        // Less strict month header detection
        return isset($row[0]) && is_string($row[0]) && 
            preg_match('/^[A-Z]+\s+\d{4}$/i', trim($row[0]));
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
    // private function isEmptyRow($row){
    //     // Convert to array if it's a Collection
    //     $rowArray = $row instanceof Collection ? $row->toArray() : $row;
        
    //     return empty(array_filter($rowArray, function ($value){
    //         return $value !== null && $value !== '';
    //     }));
    // }

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