<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;



class Employee extends Model
{
    use Notifiable; 

    protected $fillable = [
        'first_name',
        'last_name',
        'age',
        'image',
        'phone_number',
        'birthday',
        'address',
        'date_started',
        'job_category_id',
        'branch_id',
        'status',
        'is_hidden',
        'working_days',
        'email',
    ];

    protected $casts = [
        'is_hidden' => 'boolean',
        'working_days' => 'array',
        'status' => 'boolean',
    ];

    public function scopeIsVisible($query)
    {
        return $query->where('is_hidden', false);
    }

    public function equipments()
    {
        return $this->belongsTo(Equipment::class);
    }

    public function jobCategory()
    {
        return $this->belongsTo(JobCategory::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'email', 'email');
    }

    public function routeNotificationForMail($notification)
{
    return $this->email; // Ensure Employee has an email column
}


}
