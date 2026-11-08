<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class RoomStatusLog extends Model
{
    protected string $table = 'room_status_logs';

    protected array $fillable = [
        'room_id',
        'old_status',
        'new_status',
        'user_id',
        'notes'
    ];
}

