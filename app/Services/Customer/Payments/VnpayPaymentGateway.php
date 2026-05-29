<?php

namespace App\Services\Customer\Payments;

use App\Models\Order;

class VnpayPaymentGateway
{
    public function createPaymentUrl(Order $order): string
    {
        $params = [
            'vnp_Version' => '2.1.0',
            'vnp_Command' => 'pay',
            'vnp_TmnCode' => (string) config('services.vnpay.tmn_code'),
            'vnp_Amount' => (int) $order->payment_total * 100,
            'vnp_CurrCode' => 'VND',
            'vnp_TxnRef' => (string) $order->id,
            'vnp_OrderInfo' => 'Order #' . $order->id,
            'vnp_OrderType' => 'other',
            'vnp_Locale' => 'vn',
            'vnp_ReturnUrl' => (string) config('services.vnpay.return_url'),
            'vnp_IpAddr' => request()->ip() ?: '127.0.0.1',
            'vnp_CreateDate' => now()->format('YmdHis'),
        ];

        $params['vnp_SecureHash'] = $this->sign($params);

        return rtrim((string) config('services.vnpay.payment_url'), '?') . '?' . http_build_query($params);
    }

    public function verify(array $params): bool
    {
        $secureHash = $params['vnp_SecureHash'] ?? '';
        unset($params['vnp_SecureHash'], $params['vnp_SecureHashType']);

        return is_string($secureHash) && hash_equals($this->sign($params), $secureHash);
    }

    public function sign(array $params): string
    {
        ksort($params);
        $hashData = [];

        foreach ($params as $key => $value) {
            $hashData[] = urlencode($key) . '=' . urlencode((string) $value);
        }

        return hash_hmac('sha512', implode('&', $hashData), (string) config('services.vnpay.hash_secret'));
    }
}
