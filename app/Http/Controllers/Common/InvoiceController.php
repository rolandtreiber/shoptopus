<?php

namespace App\Http\Controllers\Common;

use App\Enums\AccessTokenType;
use App\Http\Controllers\Controller;
use App\Models\AccessToken;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Exception;
use Illuminate\Support\Facades\Config;

class InvoiceController extends Controller
{
    /**
     * @throws Exception
     */
    public function download(AccessToken $token)
    {
        if ($token->type != AccessTokenType::Invoice || ! $token->accessable instanceof Invoice) {
            throw new Exception('invalid_token', Config::get('api_error_codes.services.invoices.download'));
        }
        /** @var Invoice $invoice */
        $invoice = $token->accessable;

        $documentName = 'invoice-'.env('APP_NAME').'-order-'.$invoice->slug;
        $pdf = PDF::loadView('pdf.invoice', [
            'invoice' => $invoice,
            'documentName' => $documentName,
        ]);
//        return $pdf->stream($documentName.'.pdf'); // Uncomment for speedy testing
        return $pdf->download($documentName.'.pdf');
    }
}
