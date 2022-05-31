<?php

namespace Tests;

use Illuminate\Support\Facades\Storage;

class PaymentTestCase extends TestCase {

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Storage::disk('local')->putFileAs('/amazon/', './tests/PublicApi/Payments/Amazon/TestData/amazon_pay_private_key_test.pem', 'amazon_pay_private_key.pem');
    }

}
