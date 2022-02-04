<?php

declare(strict_types=1);

namespace Toby\Infrastructure\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class GeneratePdfController extends Controller
{
    public function generatePDF(): Response
    {
        $data = ["data"];
        $pdf = PDF::loadView('vacation-request-pdf', $data);
        return $pdf->stream();
    }
}
