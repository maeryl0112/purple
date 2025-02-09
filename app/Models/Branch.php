<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function services()
    {
        return $this->belongsToMany(Service::class, 'branch_service');
    }
    

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }

    public function supplies()
    {
        return $this->hasMany(Supply::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function user()
    {
        return $this->hasMany(User::class);
    }

    public function online_supplier()
    {
        return $this->hasMany(OnlineSupplier::class);
    }
}
