<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShipmentCalendar extends Model
{
    use HasFactory;

    protected $fillable = [
        'buyer',
        'contract_no',
        'laycan_start',
        'laycan_end',
        'eta',
        'vessel',
        'qty',
        'spec',
        'laycan_status',
        'discharge_port',
    ];

    protected function casts(): array
    {
        return [
            'laycan_start' => 'date',
            'laycan_end' => 'date',
            'eta' => 'date',
            'qty' => 'decimal:2',
        ];
    }
}
