<?php

namespace App\Imports;

use App\Models\Survey;
use Maatwebsite\Excel\Concerns\ToModel;

class SurveysImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return Survey|null
     */
    public function model(array $row){
        $dateStarted = $this->convertDate($row[0] ?? null);
        $dateCompleted = $this->convertDate($row[1] ?? null);
        $dateApproved = $this->convertDate($row[11] ?? null);
        $dateDelivered = $this->convertDate($row[12] ?? null);

        return new Survey([
            'date_started' => $dateStarted,
            'date_completed' => $dateCompleted,
            'location' => $row[2] ?? null,
            'survey_details' => $row[3] ?? null,
            'area' => $row[4] ?? null,
            'processed_by' => $row[5] ?? null,
            'survey' => $row[6] ?? null,
            'data_process' => $row[7] ?? null,
            'plans' => $row[8] ?? null,
            'contact_person' => $row[9] ?? null,
            'contact_no' => $row[10] ?? null,
            'date_approved' => $dateApproved,
            'date_delivered' => $dateDelivered,
        ]);
    }

    private function convertDate($date){
        if ($date) {
            $dateTime = \DateTime::createFromFormat('F j, Y', $date);
            if ($dateTime) {
                return $dateTime->format('Y-m-d');
            }
        }
        return null; // or handle invalid date as needed
    }
}


