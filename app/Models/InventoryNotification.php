<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryNotification extends Model
{
    protected $fillable = ['type', 'title', 'message', 'supply_id', 'equipment_id', 'category', 'is_read'];

    public function supply()
    {
        return $this->belongsTo(Supply::class);
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
