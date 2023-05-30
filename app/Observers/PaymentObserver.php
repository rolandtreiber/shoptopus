<?php

namespace App\Observers;

use App\Enums\PaymentType;
use App\Helpers\GeneralHelper;
use App\Models\Order;
use App\Models\Payment;
use App\Repositories\Admin\Invoice\InvoiceRepository;
use App\Repositories\Admin\Invoice\InvoiceRepositoryInterface;
use Illuminate\Support\Carbon;

class PaymentObserver
{
    private InvoiceRepositoryInterface $invoiceRepository;

    public function __construct(InvoiceRepository $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    public function creating(Payment $payment)
    {
        switch ($payment->type) {
            case PaymentType::Refund:
                $descriptionText = 'Refund of '.GeneralHelper::displayPrice($payment->amount).' ';
                break;
            default:
                $descriptionText = 'Payment of '.GeneralHelper::displayPrice($payment->amount).' ';
        }

        $descriptionText .= 'for the '.str_replace("App\Models\\", '', $payment->payable_type).' with ID "'.$payment->payable_id.'" ';
        $descriptionText .= 'processed at '.Carbon::parse($payment->created_at)->format('Y-m-d H:i:s');
        $payment->description = $descriptionText;
    }

    public function created(Payment $payment)
    {
        if ($payment->type === PaymentType::Payment && $payment->payable_type === Order::class) {
            $this->invoiceRepository->create($payment->payable);
        }
    }
}
