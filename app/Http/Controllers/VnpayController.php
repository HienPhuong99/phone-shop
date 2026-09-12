<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Services\VnpayService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class VnpayController extends Controller
{
    public function __construct(private readonly VnpayService $vnpay) {}

    /**
     * Browser-facing redirect target after the customer finishes on VNPay.
     * This is only ever used to show the customer a status message — it is
     * NOT trusted to confirm payment, because it can be spoofed by simply
     * visiting the URL with crafted query params. The IPN callback below is
     * the only authoritative source of truth for payment status.
     */
    public function return(Request $request): View
    {
        $params = $request->query();
        $isValid = $this->vnpay->isValidSignature($params);
        $order = $isValid ? Order::where('order_code', $params['vnp_TxnRef'] ?? null)->first() : null;
        $success = $isValid && ($params['vnp_ResponseCode'] ?? null) === '00';

        return view('vnpay.return', [
            'success' => $success,
            'isValid' => $isValid,
            'order' => $order,
        ]);
    }

    /**
     * Server-to-server webhook — the authoritative confirmation of payment
     * status. Verifies the HMAC-SHA512 signature, checks the order and
     * amount match, and is idempotent (a duplicate IPN call for an already
     * -confirmed order does not get reprocessed / double-charge stock).
     */
    public function ipn(Request $request): JsonResponse
    {
        $params = $request->query();

        Log::info('VNPay IPN received', $params);

        if (! $this->vnpay->isValidSignature($params)) {
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
        }

        $order = Order::where('order_code', $params['vnp_TxnRef'] ?? null)->first();

        if (! $order) {
            return response()->json(['RspCode' => '01', 'Message' => 'Order not found']);
        }

        $expectedAmount = (int) round($order->total_amount * 100);

        if ((int) ($params['vnp_Amount'] ?? 0) !== $expectedAmount) {
            return response()->json(['RspCode' => '04', 'Message' => 'Invalid amount']);
        }

        $existingPayment = $order->payment;

        if ($existingPayment && $existingPayment->status !== Payment::STATUS_PENDING) {
            return response()->json(['RspCode' => '02', 'Message' => 'Order already confirmed']);
        }

        $isSuccess = ($params['vnp_ResponseCode'] ?? null) === '00';

        DB::transaction(function () use ($order, $params, $isSuccess) {
            Payment::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'gateway' => 'vnpay',
                    'transaction_id' => $params['vnp_TransactionNo'] ?? null,
                    'amount' => $order->total_amount,
                    'status' => $isSuccess ? Payment::STATUS_SUCCESS : Payment::STATUS_FAILED,
                    'raw_response' => json_encode($params),
                ]
            );

            if ($isSuccess) {
                $order->update(['status' => Order::STATUS_PAID]);
            } else {
                $order->update(['status' => Order::STATUS_CANCELLED]);

                foreach ($order->items as $item) {
                    if ($item->variant_id) {
                        ProductVariant::whereKey($item->variant_id)->increment('stock_quantity', $item->quantity);
                    }
                }
            }
        });

        return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
    }
}
