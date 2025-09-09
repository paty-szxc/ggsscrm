<?php

namespace App\Imports;

use App\Models\ConstructionSalesRevenue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Log;

class ConstructionSalesRevenueImport implements ToCollection, WithStartRow, WithMultipleSheets
{
    private $importedCount = 0;
    private $skippedCount = 0;

    //add these public methods to access the counts
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function collection(Collection $rows)
    {
        Log::info('Starting import process', ['total_rows' => $rows->count()]);

        foreach ($rows as $index => $row) {
            $rowNumber = $index + $this->startRow() + 1;
            
            try{
                Log::debug("Processing row {$rowNumber}", ['data' => $row->toArray()]);

                if($this->isEmptyRow($row)){
                    $this->skippedCount++;
                    Log::info("Skipping empty row {$rowNumber}");
                    continue;
                }

                if($this->isMonthHeaderRow($row)){
                    $this->skippedCount++;
                    Log::info("Skipping month header row {$rowNumber}");
                    continue;
                }

                $data = $this->prepareData($row);
                
                Log::debug("Prepared data for row {$rowNumber}", $data);

                //new validation: Check if first_collection is 30% of project_cost
                $this->validateFirstCollection($rowNumber, $data);

                $record = ConstructionSalesRevenue::create($data);
                
                if($record->exists){
                    $this->importedCount++;
                    Log::info("Successfully imported row {$rowNumber}", ['id' => $record->id]);
                }
                else{
                    $this->skippedCount++;
                    Log::error("Failed to create record for row {$rowNumber}");
                }

            }
            catch(\Exception $e){
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

    private function prepareData($row): array{
        return [
            'date' => $this->convertDate($row[0]),
            'client_name_address' => $row[1] ?? null,
            'particulars' => $row[2] ?? null,
            'status_of_vat' => $row[3] ?? null,
            'receipt_no' => $row[4] ?? null,
            'project_cost' => $this->cleanNumber($row[5]),
            'amount_gross_of_vat' => $this->cleanNumber($row[6]),
            'net_of_vat' => $this->cleanNumber($row[7]),
            'vat' => $this->cleanNumber($row[8]),
            'first_date_of_collection' => $this->convertDate($row[9]),
            'first_collection' => $this->cleanNumber($row[10]),
            'second_date_of_collection' => $this->convertDate($row[11]),
            'second_collection' => $this->cleanNumber($row[12]),
            'third_date_of_collection' => $this->convertDate($row[13]),
            'third_collection' => $this->cleanNumber($row[14]),
            'fourth_date_of_collection' => $this->convertDate($row[15]),
            'fourth_collection' => $this->cleanNumber($row[16]),
            'total' => $this->cleanNumber($row[17]),
            'others' => $row[19] ?? null,
            'remarks' => $row[20] ?? null,
            'fully_paid_date' => $this->extractFullyPaidDate($row[20] ?? null),
        ];
    }

    private function validateFirstCollection(int $rowNumber, array $data)
    {
        $projectCost = $data['project_cost'];
        $firstCollection = $data['first_collection'];

        // Skip validation if either value is not a valid number
        if (!is_numeric($projectCost) || !is_numeric($firstCollection)) {
            Log::warning("Skipping 30% check on row {$rowNumber} due to non-numeric values.", ['project_cost' => $projectCost, 'first_collection' => $firstCollection]);
            return;
        }

        // Calculate the expected first collection amount (30% less 2%)
        $expectedFirstCollection = round(($projectCost * 0.30) * 0.98, 2);
        
        // Use a small tolerance for floating-point comparison
        $tolerance = 0.01; 
        if (abs($firstCollection - $expectedFirstCollection) > $tolerance) {
            Log::warning("Row {$rowNumber}: First collection (₱{$firstCollection}) is not 30% of the project cost (₱{$projectCost}) less 2%. Expected value: ₱{$expectedFirstCollection}.");
        }
    }

    private function extractFullyPaidDate($remarks){
        if(!$remarks) return null;
        
        if(preg_match('/FULLY\s+PAID\s+(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4})/i', $remarks, $matches)){
            return $this->convertDate($matches[1]);
        }
        return null;
    }

    private function cleanNumber($value){
        if(is_null($value) || $value === '') return null;
        if(is_numeric($value)) return (float) $value;
        
        //remove all non-numeric characters except decimal point
        $cleaned = preg_replace('/[^\d\.]/', '', $value);
        return $cleaned !== '' ? (float) $cleaned : null;
    }

    private function convertDate($date){
        if(empty($date)) return null;

        //handle Excel serial dates
        if(is_numeric($date)){
            $unixDate = ($date - 25569) * 86400;
            return gmdate('Y-m-d', $unixDate);
        }

        //try common date formats
        $formats = [
            'm/d/Y', 'd/m/Y', 'Y-m-d', 'F j, Y',
            'n/j/Y', 'j/n/Y', 'Y/m/d', 'm-d-Y',
            'd-m-Y', 'Y-m-d H:i:s', 'Y-m-d\TH:i:s'
        ];

        foreach($formats as $format){
            $dateTime = \DateTime::createFromFormat($format, $date);
            if($dateTime !== false){
                return $dateTime->format('Y-m-d');
            }
        }

        //fallback to strtotime
        $timestamp = strtotime($date);
        return $timestamp !== false ? date('Y-m-d', $timestamp) : null;
    }
}