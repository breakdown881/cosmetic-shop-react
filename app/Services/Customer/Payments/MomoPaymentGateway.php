<?php

namespace App\Services\Customer\Payments;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MomoPaymentGateway
{
    public function createPaymentUrl(Order $order): string
    {
        $payload = $this->createPayload($order);
        $response = Http::post((string) config('services.momo.endpoint'), $payload)->json();

        if (($response['resultCode'] ?? 99) !== 0 || empty($response['payUrl'])) {
            throw new RuntimeException($response['message'] ?? 'Could not create MoMo payment.');
        }

        return $response['payUrl'];
    }

    public function verifyResult(array $payload): bool
    {
        $signature = $payload['signature'] ?? '';

        return is_string($signature) && hash_equals($this->resultSignature($payload), $signature);
    }

    public function createPayload(Order $order): array
    {
        $requestId = 'ORDER-' . $order->id . '-' . now()->timestamp;
        $payload = [
            'partnerCode' => (string) config('services.momo.partner_code'),
            'partnerName' => config('app.name', 'Goda Shop'),
            'storeId' => 'GodaShop',
            'requestId' => $requestId,
            'amount' => (int) $order->payment_total,
            'orderId' => (string) $order->id,
            'orderInfo' => 'Order #' . $order->id,
            'redirectUrl' => (string) config('services.momo.redirect_url'),
            'ipnUrl' => (string) config('services.momo.ipn_url'),
            'lang' => 'vi',
            'requestType' => (string) config('services.momo.request_type', 'payWithMethod'),
            'autoCapture' => true,
            'extraData' => '',
        ];
        $payload['signature'] = $this->createSignature($payload);

        return $payload;
    }

    public function createSignature(array $payload): string
    {
        $raw = 'accessKey=' . config('services.momo.access_key')
            . '&amount=' . $payload['amount']
            . '&extraData=' . ($payload['extraData'] ?? '')
            . '&ipnUrl=' . $payload['ipnUrl']
            . '&orderId=' . $payload['orderId']
            . '&orderInfo=' . $payload['orderInfo']
            . '&partnerCode=' . $payload['partnerCode']
            . '&redirectUrl=' . $payload['redirectUrl']
            . '&requestId=' . $payload['requestId']
            . '&requestType=' . $payload['requestType'];

        return hash_hmac('sha256', $raw, (string) config('services.momo.secret_key'));
    }

    public function resultSignature(array $payload): string
    {
        $raw = 'accessKey=' . config('services.momo.access_key')
            . '&amount=' . ($payload['amount'] ?? '')
            . '&extraData=' . ($payload['extraData'] ?? '')
            . '&message=' . ($payload['message'] ?? '')
            . '&orderId=' . ($payload['orderId'] ?? '')
            . '&orderInfo=' . ($payload['orderInfo'] ?? '')
            . '&orderType=' . ($payload['orderType'] ?? '')
            . '&partnerCode=' . ($payload['partnerCode'] ?? '')
            . '&payType=' . ($payload['payType'] ?? '')
            . '&requestId=' . ($payload['requestId'] ?? '')
            . '&responseTime=' . ($payload['responseTime'] ?? '')
            . '&resultCode=' . ($payload['resultCode'] ?? '')
            . '&transId=' . ($payload['transId'] ?? '');

        return hash_hmac('sha256', $raw, (string) config('services.momo.secret_key'));
    }
}
