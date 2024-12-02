<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'image',
    ];

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function equipments()
    {
        return $this->hasMany(Equipment::class);
    }

    public function supplies()
    {
        return $this->hasMany(Supply::class);
    }
}
