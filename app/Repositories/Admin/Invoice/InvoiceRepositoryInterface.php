<?php

namespace App\Repositories\Admin\Invoice;

use App\Models\Invoice;
use App\Models\Order;

interface InvoiceRepositoryInterface
{
    public function create(Order $order): Invoice;
}
