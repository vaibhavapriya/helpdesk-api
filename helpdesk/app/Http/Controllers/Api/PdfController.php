<?php

//composer require barryvdh/laravel-dompdf
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function downloadInvoice(Request $request)
    {
        // Example data — this can be dynamic
        $data = [
            'invoiceNumber' => '12345',
            'customerName' => 'John Doe',
        ];

        // Generate the PDF from a Blade view
        $pdf = Pdf::loadView('pdf.invoice', $data);

        // Return the PDF as an inline response
        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="invoice.pdf"');
    }
    //->header('Content-Disposition', 'attachment; filename="invoice.pdf"');

}
