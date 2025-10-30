<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/'.ltrim($this->avatar, '/'));
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=E5F0FF&color=1E3A8A&bold=true';
    }

    /**
     * Get the vehicles created by the user.
     */
    public function createdVehicles()
    {
        return $this->hasMany(Vehicle::class, 'created_by');
    }

    /**
     * Get the tasks assigned to the user.
     */
    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    /**
     * Get the tasks created by the user.
     */
    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    /**
     * Get the vehicle photos uploaded by the user.
     */
    public function uploadedVehiclePhotos()
    {
        return $this->hasMany(VehiclePhoto::class, 'uploaded_by');
    }

    /**
     * Get the task attachments uploaded by the user.
     */
    public function uploadedTaskAttachments()
    {
        return $this->hasMany(TaskAttachment::class, 'uploaded_by');
    }
}
