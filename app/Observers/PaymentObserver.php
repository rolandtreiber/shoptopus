<?php

namespace App\Observers;

use App\Enums\PaymentType;
use App\Helpers\GeneralHelper;
use App\Models\Payment;
use Illuminate\Support\Carbon;

class PaymentObserver
{
    /**
     * @param Payment $payment
     */
    public function creating(Payment $payment)
    {
        switch ($payment->type) {
            case PaymentType::Refund:
                $descriptionText = 'Refund of '.GeneralHelper::displayPrice($payment->amount).' ';
                break;
            default:
                $descriptionText = 'Payment of '.GeneralHelper::displayPrice($payment->amount).' ';
        }

        $descriptionText .= 'for the '.str_replace("App\Models\\", "", $payment->payable_type) . ' with ID "'.$payment->payable_id.'" ';
        $descriptionText .= 'processed at '.Carbon::parse($payment->created_at)->format('Y-m-d H:i:s');
        $payment->description = $descriptionText;
    }
}
