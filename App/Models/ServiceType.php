<?php

namespace App\Models;

use App\Core\Model;

class ServiceType extends Model
{
    protected string $table = 'service_types';

    protected array $fillable = [
        'name',
        'slug',
        'description',
    ];
}

