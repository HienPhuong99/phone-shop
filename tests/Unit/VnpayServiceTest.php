<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Services\VnpayService;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class VnpayServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('vnpay.tmn_code', 'TESTCODE');
        Config::set('vnpay.hash_secret', 'TESTSECRET123');
        Config::set('vnpay.url', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        Config::set('vnpay.return_url', 'http://localhost/vnpay/return');
        Config::set('vnpay.version', '2.1.0');
        Config::set('vnpay.command', 'pay');
        Config::set('vnpay.curr_code', 'VND');
        Config::set('vnpay.locale', 'vn');
    }

    public function test_signature_round_trips_through_generated_payment_url(): void
    {
        $order = new Order([
            'order_code' => 'DH123456',
            'total_amount' => 250000,
        ]);

        $service = new VnpayService;
        $url = $service->buildPaymentUrl($order, '127.0.0.1');

        parse_str(parse_url($url, PHP_URL_QUERY), $params);

        $this->assertSame('TESTCODE', $params['vnp_TmnCode']);
        $this->assertSame('25000000', $params['vnp_Amount']);
        $this->assertTrue($service->isValidSignature($params));
    }

    public function test_signature_is_rejected_when_amount_is_tampered_with(): void
    {
        $order = new Order([
            'order_code' => 'DH123456',
            'total_amount' => 250000,
        ]);

        $service = new VnpayService;
        $url = $service->buildPaymentUrl($order, '127.0.0.1');

        parse_str(parse_url($url, PHP_URL_QUERY), $params);

        $params['vnp_Amount'] = '1';

        $this->assertFalse($service->isValidSignature($params));
    }

    public function test_signature_is_rejected_when_hash_secret_differs(): void
    {
        $order = new Order([
            'order_code' => 'DH123456',
            'total_amount' => 250000,
        ]);

        $service = new VnpayService;
        $url = $service->buildPaymentUrl($order, '127.0.0.1');

        parse_str(parse_url($url, PHP_URL_QUERY), $params);

        Config::set('vnpay.hash_secret', 'A_DIFFERENT_SECRET');

        $this->assertFalse($service->isValidSignature($params));
    }

    public function test_missing_secure_hash_is_rejected(): void
    {
        $service = new VnpayService;

        $this->assertFalse($service->isValidSignature(['vnp_TxnRef' => 'DH123456']));
    }
}
