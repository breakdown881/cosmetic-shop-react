<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CustomerPaymentGatewayTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'scout.driver' => 'database',
            'services.vnpay.tmn_code' => 'TESTVNPAY',
            'services.vnpay.hash_secret' => 'vnpay-secret',
            'services.vnpay.payment_url' => 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html',
            'services.vnpay.return_url' => 'http://localhost/payments/vnpay/return',
            'services.momo.partner_code' => 'MOMOTEST',
            'services.momo.access_key' => 'momo-access',
            'services.momo.secret_key' => 'momo-secret',
            'services.momo.endpoint' => 'https://test-payment.momo.vn/v2/gateway/api/create',
            'services.momo.redirect_url' => 'http://localhost/payments/momo/return',
            'services.momo.ipn_url' => 'http://localhost/payments/momo/ipn',
            'services.momo.request_type' => 'payWithMethod',
        ]);
    }

    public function test_payment_gateway_uses_service_gateway_and_repository_pattern(): void
    {
        $controller = file_get_contents(app_path('Http/Controllers/Customer/PaymentController.php'));
        $service = file_get_contents(app_path('Services/Customer/CustomerPaymentService.php'));
        $repository = file_get_contents(app_path('Repositories/Customer/CustomerPaymentRepository.php'));

        $this->assertStringContainsString('CustomerPaymentService', $controller);
        $this->assertStringNotContainsString('use App\\Models\\', $controller);
        $this->assertStringNotContainsString('Order::', $controller);

        $this->assertStringContainsString('CustomerPaymentRepository', $service);
        $this->assertStringContainsString('VnpayPaymentGateway', $service);
        $this->assertStringContainsString('MomoPaymentGateway', $service);
        $this->assertStringNotContainsString('Order::', $service);

        $this->assertStringContainsString('Order::query()', $repository);
    }

    public function test_vnpay_checkout_returns_signed_payment_url(): void
    {
        $customer = User::factory()->create();
        $product = $this->product('VNPay Serum', 300000);

        $response = $this->actingAs($customer)
            ->withSession(['customer_cart' => [$product->id => 1]])
            ->postJson('/checkout', $this->checkoutPayload(['payment_method' => 2]))
            ->assertAccepted()
            ->assertJsonPath('data.status', 'COMPLETED')
            ->assertJsonPath('data.payment.gateway', 'vnpay')
            ->assertJsonPath('data.payment.method', 'redirect');

        $paymentUrl = $response->json('data.payment.redirect_url');
        $this->assertStringStartsWith('https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?', $paymentUrl);
        $this->assertStringContainsString('vnp_TmnCode=TESTVNPAY', $paymentUrl);
        $this->assertStringContainsString('vnp_Amount=30000000', $paymentUrl);
        $this->assertStringContainsString('vnp_SecureHash=', $paymentUrl);

        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'payment_method' => 2,
            'payment_gateway' => 'vnpay',
            'payment_status' => 'PENDING',
            'payment_total' => 300000,
        ]);
    }

    public function test_momo_checkout_posts_signed_request_and_returns_pay_url(): void
    {
        Http::fake([
            'https://test-payment.momo.vn/*' => Http::response([
                'resultCode' => 0,
                'payUrl' => 'https://test-payment.momo.vn/pay/demo',
            ], 200),
        ]);

        $customer = User::factory()->create();
        $product = $this->product('Momo Serum', 450000);

        $this->actingAs($customer)
            ->withSession(['customer_cart' => [$product->id => 1]])
            ->postJson('/checkout', $this->checkoutPayload(['payment_method' => 3]))
            ->assertAccepted()
            ->assertJsonPath('data.status', 'COMPLETED')
            ->assertJsonPath('data.payment.gateway', 'momo')
            ->assertJsonPath('data.payment.redirect_url', 'https://test-payment.momo.vn/pay/demo');

        Http::assertSent(function ($request) {
            $payload = $request->data();

            return $request->url() === 'https://test-payment.momo.vn/v2/gateway/api/create'
                && $payload['partnerCode'] === 'MOMOTEST'
                && $payload['amount'] === 450000
                && $payload['requestType'] === 'payWithMethod'
                && isset($payload['signature']);
        });

        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'payment_method' => 3,
            'payment_gateway' => 'momo',
            'payment_status' => 'PENDING',
            'payment_total' => 450000,
        ]);
    }

    public function test_online_payment_failure_marks_checkout_request_failed(): void
    {
        Http::fake([
            'https://test-payment.momo.vn/*' => Http::response([
                'resultCode' => 99,
                'message' => 'Payment gateway unavailable.',
            ], 200),
        ]);

        $customer = User::factory()->create();
        $product = $this->product('Failed Momo Serum', 450000);

        $this->actingAs($customer)
            ->withSession(['customer_cart' => [$product->id => 1]])
            ->postJson('/checkout', $this->checkoutPayload(['payment_method' => 3]))
            ->assertAccepted()
            ->assertJsonPath('data.status', 'FAILED');

        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'payment_method' => 3,
            'payment_gateway' => 'momo',
            'payment_status' => 'FAILED',
        ]);
    }

    public function test_vnpay_return_verifies_signature_and_marks_order_paid(): void
    {
        $order = $this->order('vnpay', 2, 300000);
        $params = [
            'vnp_Amount' => '30000000',
            'vnp_BankCode' => 'NCB',
            'vnp_OrderInfo' => 'Order #' . $order->id,
            'vnp_ResponseCode' => '00',
            'vnp_TmnCode' => 'TESTVNPAY',
            'vnp_TransactionNo' => '14123456',
            'vnp_TxnRef' => (string) $order->id,
        ];
        $params['vnp_SecureHash'] = $this->vnpaySignature($params);

        $this->get('/payments/vnpay/return?' . http_build_query($params))
            ->assertRedirect('/orders/' . $order->id);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'PAID',
            'payment_reference' => '14123456',
            'status' => 'PROCESSING',
        ]);
    }

    public function test_momo_ipn_verifies_signature_and_marks_order_paid(): void
    {
        $order = $this->order('momo', 3, 450000);
        $payload = [
            'partnerCode' => 'MOMOTEST',
            'orderId' => (string) $order->id,
            'requestId' => 'REQ-' . $order->id,
            'amount' => 450000,
            'orderInfo' => 'Order #' . $order->id,
            'orderType' => 'momo_wallet',
            'transId' => 999888,
            'resultCode' => 0,
            'message' => 'Successful.',
            'payType' => 'qr',
            'responseTime' => 1710000000000,
            'extraData' => '',
        ];
        $payload['signature'] = $this->momoResultSignature($payload);

        $this->postJson('/payments/momo/ipn', $payload)
            ->assertOk()
            ->assertJsonPath('message', 'OK');

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'payment_status' => 'PAID',
            'payment_reference' => '999888',
            'status' => 'PROCESSING',
        ]);
    }

    private function checkoutPayload(array $overrides = []): array
    {
        return array_merge([
            'shipping_fullname' => 'Nguyen Van A',
            'shipping_mobile' => '0900111222',
            'shipping_ward_id' => '001',
            'shipping_housenumber_street' => '123 Beauty Street',
            'payment_method' => 0,
        ], $overrides);
    }

    private function product(string $name, int $price): Product
    {
        $brand = Brand::create(['name' => 'Payment Brand ' . uniqid(), 'status' => 1, 'created_by' => 1]);
        $category = Category::create(['name' => 'Payment Category ' . uniqid(), 'status' => 1, 'created_by' => 1]);

        return Product::withoutSyncingToSearch(fn () => Product::query()->create([
            'code' => 'PAY-' . uniqid(),
            'name' => $name,
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'price' => $price,
            'discount_percentage' => 0,
            'discount_from_date' => now()->toDateString(),
            'discount_to_date' => now()->addDay()->toDateString(),
            'media_id' => 1,
            'inventory_qty' => 10,
            'description' => 'Payment product description',
            'star' => 4.5,
            'featured' => 0,
            'created_by' => 1,
            'status' => 1,
        ]));
    }

    private function order(string $gateway, int $method, int $total): Order
    {
        return Order::create([
            'staff_id' => 1,
            'customer_id' => User::factory()->create()->id,
            'shipping_fullname' => 'Payment Customer',
            'shipping_mobile' => '0900111222',
            'payment_method' => $method,
            'shipping_ward_id' => '001',
            'shipping_housenumber_street' => '123 Payment Street',
            'shipping_fee' => 0,
            'feeship_id' => null,
            'delivered_date' => now()->toDateString(),
            'price_total' => $total,
            'discount_code' => '',
            'discount_amount' => 0,
            'sub_total' => $total,
            'tax' => 0,
            'price_inc_tax_total' => $total,
            'voucher_code' => '',
            'voucher_amount' => 0,
            'payment_total' => $total,
            'status' => 'PENDING',
            'payment_gateway' => $gateway,
            'payment_status' => 'PENDING',
        ]);
    }

    private function vnpaySignature(array $params): string
    {
        ksort($params);
        $hashData = [];

        foreach ($params as $key => $value) {
            $hashData[] = urlencode($key) . '=' . urlencode((string) $value);
        }

        return hash_hmac('sha512', implode('&', $hashData), 'vnpay-secret');
    }

    private function momoResultSignature(array $payload): string
    {
        $raw = 'accessKey=momo-access'
            . '&amount=' . $payload['amount']
            . '&extraData=' . $payload['extraData']
            . '&message=' . $payload['message']
            . '&orderId=' . $payload['orderId']
            . '&orderInfo=' . $payload['orderInfo']
            . '&orderType=' . $payload['orderType']
            . '&partnerCode=' . $payload['partnerCode']
            . '&payType=' . $payload['payType']
            . '&requestId=' . $payload['requestId']
            . '&responseTime=' . $payload['responseTime']
            . '&resultCode=' . $payload['resultCode']
            . '&transId=' . $payload['transId'];

        return hash_hmac('sha256', $raw, 'momo-secret');
    }
}
