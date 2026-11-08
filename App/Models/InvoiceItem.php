<?php

namespace App\Models;

use App\Core\Model;

class InvoiceItem extends Model
{
    protected string $table = 'invoice_items';

    protected array $fillable = [
        'invoice_id',
        'type',
        'reference_id',
        'amount',
        'notes',
    ];
}

