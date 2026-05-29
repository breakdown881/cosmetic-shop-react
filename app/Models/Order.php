<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public const STATUSES = [
        'PENDING',
        'PROCESSING',
        'SHIPPING',
        'COMPLETE',
        'CANCELLED',
        'REFUNDED',
        'FAILED',
    ];

    public const PAYMENT_METHODS = [
        0 => 'Cash',
        1 => 'Bank transfer',
        2 => 'VNPay',
        3 => 'MoMo',
    ];

    protected $fillable = [
        'staff_id',
        'customer_id',
        'shipping_fullname',
        'shipping_mobile',
        'payment_method',
        'payment_gateway',
        'payment_status',
        'payment_reference',
        'paid_at',
        'shipping_ward_id',
        'shipping_housenumber_street',
        'shipping_fee',
        'feeship_id',
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
        'note',
    ];

    protected $casts = [
        'staff_id' => 'integer',
        'customer_id' => 'integer',
        'payment_method' => 'integer',
        'paid_at' => 'datetime',
        'shipping_fee' => 'integer',
        'feeship_id' => 'integer',
        'delivered_date' => 'date',
        'price_total' => 'integer',
        'discount_amount' => 'integer',
        'sub_total' => 'integer',
        'tax' => 'integer',
        'price_inc_tax_total' => 'integer',
        'voucher_amount' => 'integer',
        'payment_total' => 'integer',
    ];

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function staff()
    {
        return $this->belongsTo(Admin::class, 'staff_id');
    }

    public function feeShip()
    {
        return $this->belongsTo(Transport::class, 'feeship_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
