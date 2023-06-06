<?php

namespace Tests\PublicApi\Invoice;

use App\Models\AccessToken;
use App\Models\Invoice;
use App\Models\User;
use Tests\InvoicesTestCase;

/**
 * @group invoices
 */
class InvoicesTest extends InvoicesTestCase
{
    /**
     * @test
     */
    public function test_invoice_can_be_downloaded(): void
    {
        $this->createPayment();
        $accessToken = AccessToken::where('accessable_type', Invoice::class)->where('accessable_id', $this->invoice->id)->first();
        $response = $this->get(route('invoice.download', ['token' => $accessToken->token]));
        $response->assertDownload('invoice-'.env('APP_NAME').'-order-'.$this->invoice->slug.'.pdf');
    }

    /**
     * @test
     */
    public function test_invalid_access_token_throws_error(): void
    {
        $this->createPayment();
        $accessToken = AccessToken::where('accessable_type', Invoice::class)->where('accessable_id', $this->invoice->id)->first();
        $accessToken->accessable_type = User::class;
        $accessToken->save();
        $response = $this->get(route('invoice.download', ['token' => $accessToken->token]))->json();
        $this->assertEquals($response['error_code'], 3000);
        $this->assertEquals($response['developer_message'], 'invalid_token');
    }
}
