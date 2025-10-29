<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'work_date',
        'work_time',
        'status',
        'priority',
        'vehicle_id',
        'assigned_to',
        'created_by',
        'due_date',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'work_date' => 'date',
            'work_time' => 'datetime:H:i',
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Get the vehicle that owns the task.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the user assigned to the task.
     */
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the user who created the task.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the attachments for the task.
     */
    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }
}