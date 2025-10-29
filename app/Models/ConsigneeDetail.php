<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsigneeDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'name',
        'address',
        'phone',
        'email',
    ];

    /**
     * Get the vehicle that owns the consignee details.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
}