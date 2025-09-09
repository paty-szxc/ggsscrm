<?php

namespace App\Imports;

use App\Models\ConstructionExpenses;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ConstructionExpensesImport implements ToCollection
{
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows){
        $rows->shift();
        $rows->shift();

        if($rows->count() > 6){
            $rows = $rows->slice(0, -6);
        }

        //find the index of the last row that has a non-empty date
        $lastVoucherIndex = null;
        foreach ($rows as $index => $row) {
            //check if date column exists and is not empty
            if(isset($row[1]) && trim($row[1]) !== ''){
                $lastVoucherIndex = $index;
            }
        }

        //iterate through the rows and import valid ones
        foreach($rows as $index => $row){
            //skip empty rows
            if($this->isEmptyRow($row)){
                continue;
            }

            //skip rows containing "TOTAL" (case insensitive)
            foreach($row as $cell){
                if(stripos($cell, 'TOTAL') !== false){
                    continue 2;
                }
            }

            //skip rows after the last date
            if($lastVoucherIndex !== null && $index > $lastVoucherIndex){
                continue;
            }

            $date_voucher = $this->convertDate($row[1] ?? null);
            ConstructionExpenses::create([
                'voucher_no' => $row[0] ?? null,
                'date' => $date_voucher,
                'suppliers_name' => $row[2] ?? null,
                'address_brgy_city' => $row[3] ?? null,
                'status_of_vat' => $row[4] ?? null,
                'tin' => $row[5] ?? null,
                'particulars' => $row[6] ?? null,
                'meals_office_survey' => $row[7] ?? null,
                'construction_survey_supplies' => $row[8] ?? null,
                'repairs_maintenance' => $row[9] ?? null,
                'office_supplies' => $row[10] ?? null,
                'gasoline_oil' => $row[11] ?? null,
                'utilities' => $row[12] ?? null,
                'parking_fee' => $row[13] ?? null,
                'toll_fee' => $row[14] ?? null,
                'permits_certification_tax' => $row[15] ?? null,
                'transportation' => $row[16] ?? null,
                'budget' => $row[17] ?? null,
                'representation_expense_personal' => $row[18] ?? null,
                'others_staff_personal' => $row[19] ?? null,
                'amount_gross_of_vat' => $row[20] ?? null,
                'net_of_vat' => $row[21] ?? null,
                'vat' => $row[22] ?? null
            ]);
        }
    }



    private function isEmptyRow($row){
        //convert to array if it's a Collection
        $rowArray = $row instanceof Collection ? $row->toArray() : $row;
        return empty(array_filter($rowArray, function ($value){
            return $value !== null && $value !== '';
        }));
    }

    private function convertDate($date){
        if(empty($date)){
            return null;
        }

        $formats = ['F j, Y', 'M j, Y', 'Y-m-d', 'm/d/Y', 'd/m/Y'];
        foreach($formats as $format){
            $dateTime = \DateTime::createFromFormat($format, $date);
            if($dateTime !== false){
                return $dateTime->format('Y-m-d');
            }
        }
        return null;
    }
}