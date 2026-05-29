<?php

namespace App\Services\Customer;

class CustomerNavigationService
{
    public function navItems(): array
    {
        return [
            ['label' => 'Trang chủ', 'href' => '/'],
            ['label' => 'Sản phẩm', 'href' => '/products'],
            ['label' => 'Khuyen mai', 'href' => '/promotions'],
            ['label' => 'Giỏ hàng', 'href' => '/cart'],
            ['label' => 'Đơn hàng', 'href' => '/orders'],
            ['label' => 'Tài khoản', 'href' => '/account'],
        ];
    }
}
