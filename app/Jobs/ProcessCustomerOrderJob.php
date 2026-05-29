<?php

namespace App\Jobs;

use App\Models\CustomerCheckoutRequest;
use App\Repositories\Customer\CustomerCheckoutRepository;
use App\Services\Customer\CustomerPaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessCustomerOrderJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(public readonly int $checkoutRequestId)
    {
        $this->onConnection(config('queue.order_connection', 'rabbitmq'));
        $this->onQueue(config('queue.order_queue', 'orders'));
    }

    public function handle(CustomerCheckoutRepository $checkoutRepository, CustomerPaymentService $paymentService): void
    {
        $checkoutRequest = $checkoutRepository->findCheckoutRequest($this->checkoutRequestId);

        if ($checkoutRequest->status === CustomerCheckoutRequest::STATUS_COMPLETED) {
            return;
        }

        $checkoutRepository->markCheckoutProcessing($checkoutRequest);

        try {
            $order = $checkoutRepository->createOrderWithItems(
                $checkoutRequest->order_data,
                $checkoutRequest->items,
            );
            $payment = $paymentService->initiate($order);

            $checkoutRepository->markCheckoutCompleted($checkoutRequest, $order, $payment);

            Log::info('Customer order created from RabbitMQ checkout queue.', [
                'checkout_request_id' => $checkoutRequest->id,
                'order_id' => $order->id,
                'customer_id' => $order->customer_id,
                'payment_total' => $order->payment_total,
            ]);
        } catch (Throwable $exception) {
            $checkoutRepository->markCheckoutFailed($checkoutRequest, $exception->getMessage());

            Log::error('Customer order queue processing failed.', [
                'checkout_request_id' => $checkoutRequest->id,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
