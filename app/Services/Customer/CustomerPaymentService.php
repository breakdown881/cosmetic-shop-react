<?php

namespace App\Services\Customer;

use App\Models\Order;
use App\Repositories\Customer\CustomerPaymentRepository;
use App\Services\Customer\Payments\MomoPaymentGateway;
use App\Services\Customer\Payments\VnpayPaymentGateway;
use Throwable;

class CustomerPaymentService
{
    public function __construct(
        private readonly CustomerPaymentRepository $payments,
        private readonly VnpayPaymentGateway $vnpay,
        private readonly MomoPaymentGateway $momo,
    ) {}

    public function initiate(Order $order): ?array
    {
        return match ((int) $order->payment_method) {
            2 => $this->vnpayPayment($order),
            3 => $this->momoPayment($order),
            default => null,
        };
    }

    public function handleVnpayReturn(array $params): Order
    {
        abort_unless($this->vnpay->verify($params), 400, 'Invalid VNPay signature.');

        $order = $this->payments->findOrder($params['vnp_TxnRef'] ?? 0);

        if (($params['vnp_ResponseCode'] ?? '') === '00') {
            return $this->payments->markPaid($order, (string) ($params['vnp_TransactionNo'] ?? ''));
        }

        return $this->payments->markFailed($order, (string) ($params['vnp_TransactionNo'] ?? ''));
    }

    public function handleMomoResult(array $payload): Order
    {
        abort_unless($this->momo->verifyResult($payload), 400, 'Invalid MoMo signature.');

        $order = $this->payments->findOrder($payload['orderId'] ?? 0);

        if ((int) ($payload['resultCode'] ?? 99) === 0) {
            return $this->payments->markPaid($order, (string) ($payload['transId'] ?? ''));
        }

        return $this->payments->markFailed($order, (string) ($payload['transId'] ?? ''));
    }

    private function vnpayPayment(Order $order): array
    {
        $this->payments->markPending($order, 'vnpay');

        return [
            'method' => 'redirect',
            'gateway' => 'vnpay',
            'redirect_url' => $this->vnpay->createPaymentUrl($order),
        ];
    }

    private function momoPayment(Order $order): array
    {
        $this->payments->markPending($order, 'momo');

        try {
            $redirectUrl = $this->momo->createPaymentUrl($order);
        } catch (Throwable $exception) {
            $this->payments->markFailed($order);

            throw $exception;
        }

        return [
            'method' => 'redirect',
            'gateway' => 'momo',
            'redirect_url' => $redirectUrl,
        ];
    }
}
