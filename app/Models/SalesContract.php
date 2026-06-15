<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesContract extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_number',
        'buyer_name',
        'buyer_reference',
        'seller_entity',
        'market_type',
        'pic_marketing',
        'submission_date',
        'submitted_by',
        'draft_status',
        'commodity',
        'contract_quantity_mt',
        'sales_quantity_mt',
        'shipment_period',
        'incoterms',
        'gar_gcv',
        'actual_gar',
        'total_moisture',
        'inherent_moisture',
        'ash',
        'ash_limit',
        'sulphur',
        'sulphur_limit',
        'size',
        'pricing_basis',
        'price_type',
        'fixed_price',
        'price_currency',
        'formula_price',
        'minus_plus',
        'payment_term_summary',
        'shipment_no',
        'barges',
        'eta',
        'laycan_start_date',
        'laycan_end_date',
        'load_port',
        'destination_port',
        'tug_boat_name',
        'barge_vessel_name',
        'barge_vessel_agent',
        'dmo_status',
        'surveyor',
        'laycan_status',
        'approval_status',
        'approved_by',
        'approval_date',
        'revision_note',
        'final_status',
        'contract_file_path',
        'contract_file_name',
    ];

    protected function casts(): array
    {
        return [
            'submission_date' => 'date',
            'eta' => 'date',
            'laycan_start_date' => 'date',
            'laycan_end_date' => 'date',
            'approval_date' => 'date',
            'contract_quantity_mt' => 'decimal:2',
            'sales_quantity_mt' => 'decimal:2',
            'fixed_price' => 'decimal:2',
            'minus_plus' => 'decimal:2',
        ];
    }
}
