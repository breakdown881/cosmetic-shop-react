<?php

namespace App\Services\Customer;

class CustomerNavigationService
{
    public function navItems(): array
    {
        return [
            ['label' => 'Trang chủ', 'href' => '/'],
            ['label' => 'Tất cả sản phẩm', 'href' => '/products'],
            ['label' => 'Khuyến mãi', 'href' => '/promotions'],
            ['label' => 'Giỏ hàng', 'href' => '/cart'],
            ['label' => 'Đơn hàng', 'href' => '/orders'],
            ['label' => 'Tài khoản', 'href' => '/account'],
        ];
    }
}
