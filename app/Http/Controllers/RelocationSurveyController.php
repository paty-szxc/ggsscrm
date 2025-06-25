<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;

class RelocationSurveyController extends Controller
{
    public function generatePDF()
    {
        $data = [
            'title' => 'Employee Relocation Survey',
            'date' => now()->format('m/d/Y')
        ];

        $pdf = FacadePdf::loadView('relocation_survey', $data);
        $pdf->setOption('isPhpEnabled', true); 
        $pdf->setOption('isHtml5ParserEnabled', true);
        
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('relocation_survey.pdf');
    }
}