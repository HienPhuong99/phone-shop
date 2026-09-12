<?php

namespace App\Services;

use App\Models\Order;

class VnpayService
{
    /**
     * Build the full VNPay payment URL for the given order, signed with
     * HMAC-SHA512 over the sorted, url-encoded query string as required
     * by the VNPay integration spec.
     */
    public function buildPaymentUrl(Order $order, string $ipAddress): string
    {
        $params = [
            'vnp_Version' => config('vnpay.version'),
            'vnp_Command' => config('vnpay.command'),
            'vnp_TmnCode' => config('vnpay.tmn_code'),
            'vnp_Amount' => (int) round($order->total_amount * 100),
            'vnp_CurrCode' => config('vnpay.curr_code'),
            'vnp_TxnRef' => $order->order_code,
            'vnp_OrderInfo' => 'Thanh toan don hang '.$order->order_code,
            'vnp_OrderType' => 'other',
            'vnp_Locale' => config('vnpay.locale'),
            'vnp_ReturnUrl' => config('vnpay.return_url'),
            'vnp_IpAddr' => $ipAddress,
            'vnp_CreateDate' => now()->format('YmdHis'),
            'vnp_ExpireDate' => now()->addMinutes(15)->format('YmdHis'),
        ];

        ksort($params);

        $hashData = $this->buildQueryString($params);
        $secureHash = hash_hmac('sha512', $hashData, (string) config('vnpay.hash_secret'));

        return config('vnpay.url').'?'.$hashData.'&vnp_SecureHash='.$secureHash;
    }

    /**
     * Verify that the vnp_SecureHash sent back by VNPay (on the return URL
     * or the IPN callback) matches a hash we recompute from the other
     * params using our hash secret. This is the only trustworthy way to
     * confirm a response actually came from VNPay.
     */
    public function isValidSignature(array $params): bool
    {
        $receivedHash = $params['vnp_SecureHash'] ?? null;

        if (! $receivedHash) {
            return false;
        }

        unset($params['vnp_SecureHash'], $params['vnp_SecureHashType']);

        ksort($params);

        $hashData = $this->buildQueryString($params);
        $expectedHash = hash_hmac('sha512', $hashData, (string) config('vnpay.hash_secret'));

        return hash_equals($expectedHash, $receivedHash);
    }

    private function buildQueryString(array $params): string
    {
        return http_build_query($params, '', '&', PHP_QUERY_RFC3986);
    }
}
