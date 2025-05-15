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
        foreach ($rows as $row) {
            //skip empty rows
            if($this->isEmptyRow($row)) {
                continue;
            }

            $dateOfSurvey = $this->convertDate($row[0] ?? null);
            $firstDateOfCollection = $this->convertDate($row[6] ?? null);
            $secondDateOfCollection = $this->convertDate($row[8] ?? null);
            $thirdDateOfCollection = $this->convertDate($row[10] ?? null);
            $fourthDateOfCollection = $this->convertDate($row[12] ?? null);
            $fullyPaidDate = $this->convertDate($row[17] ?? null);

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
                'receivable_bal' => $this->cleanNumber($row[15] ?? null),
                'withholding_tax' => $row[16] ?? null,
                'fully_paid_date' => $fullyPaidDate,
            ]);
        }
    }

    /**
     * Check if a row is empty
     */
    private function isEmptyRow($row)
    {
        // Convert to array if it's a Collection
        $rowArray = $row instanceof Collection ? $row->toArray() : $row;
        
        return empty(array_filter($rowArray, function ($value) {
            return $value !== null && $value !== '';
        }));
    }

    private function cleanNumber($value)
    {
        if (is_null($value)) {
            return null;
        }
        
        // Remove thousands separators and convert to float
        return (float) str_replace(',', '', $value);
    }

    private function convertDate($date)
    {
        if ($date) {
            $dateTime = \DateTime::createFromFormat('F j, Y', $date);
            if ($dateTime) {
                return $dateTime->format('Y-m-d');
            }
        }
        return null;
    }
}