<?php

namespace App\Models;

use App\Core\Model;

/** Model BookingServiceItem - Bảng booking_service. Chỉ thao tác DB (CRUD). */
class BookingServiceItem extends Model
{
    protected string $table = 'booking_service';

    protected array $fillable = [
        'booking_id',
        'service_id',
        'quantity',
        'price_at_time',
        'total_amount',
        'status',
    ];
}

