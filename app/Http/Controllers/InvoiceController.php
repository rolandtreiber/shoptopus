<?php

namespace App\Http\Controllers;

use App\Enums\AccessTokenType;
use App\Exceptions\InvalidAccessTokenException;
use App\Models\AccessToken;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class InvoiceController extends Controller
{
    /**
     * @throws InvalidAccessTokenException
     */
    public function download(AccessToken $token)
    {
        if ($token->type != AccessTokenType::Invoice || !$token->accessable instanceof Invoice) {
            throw new InvalidAccessTokenException();
        }
        /** @var Invoice $invoice */
        $invoice = $token->accessable;

        $documentName = 'invoice-'.env('APP_NAME').'-order-'.$invoice->slug;
        $pdf = PDF::loadView('pdf.invoice', [
            'invoice' => $invoice,
            'documentName' => $documentName
        ]);
        return $pdf->stream($documentName.'.pdf');
//        return $pdf->download($documentName.'.pdf');
    }
}
