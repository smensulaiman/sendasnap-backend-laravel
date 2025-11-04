<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'serial_number',
        'make',
        'model',
        'chassis_model',
        'cc',
        'year',
        'color',
        'vehicle_buy_date',
        'auction_ship_number',
        'net_weight',
        'area',
        'length',
        'width',
        'height',
        'plate_number',
        'buying_price',
        'expected_yard_date',
        'rikso_from',
        'rikso_to',
        'rikso_cost',
        'rikso_company',
        'auction_sheet',
        'tohon_copy',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'vehicle_buy_date' => 'date',
            'expected_yard_date' => 'date',
            'net_weight' => 'decimal:2',
            'length' => 'decimal:2',
            'width' => 'decimal:2',
            'height' => 'decimal:2',
            'buying_price' => 'decimal:2',
            'rikso_cost' => 'decimal:2',
        ];
    }

    /**
     * Get the user who created the vehicle.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the photos for the vehicle.
     */
    public function photos()
    {
        return $this->hasMany(VehiclePhoto::class);
    }

    /**
     * Get the consignee details for the vehicle.
     */
    public function consigneeDetails()
    {
        return $this->hasOne(ConsigneeDetail::class);
    }

    /**
     * Get the tasks for the vehicle.
     */
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}