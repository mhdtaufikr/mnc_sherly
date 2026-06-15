<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesContractApproval extends Model
{
    use HasFactory;

    protected $fillable = [
        'sales_contract_id',
        'user_id',
        'approval_order',
        'approval_stage',
        'approval_group',
        'position',
        'approver_name',
        'approver_username',
        'status',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
        ];
    }

    public function salesContract()
    {
        return $this->belongsTo(SalesContract::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
