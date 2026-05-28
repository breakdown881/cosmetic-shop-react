<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'customer_id',
        'shipping_fullname',
        'shipping_mobile',
        'payment_method',
        'shipping_ward_id',
        'shipping_housenumber_street',
        'shipping_fee',
        'delivered_date',
        'price_total',
        'discount_code',
        'discount_amount',
        'sub_total',
        'tax',
        'price_inc_tax_total',
        'voucher_code',
        'voucher_amount',
        'payment_total',
        'status',
    ];
}
