<?php

namespace App\Models;

use App\Enums\UserRolesEnum;
use App\Jobs\SendAppointmentConfirmationMailJob;
use App\Jobs\SendNewServicePromoMailJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Appointment extends Model
{
    protected $fillable = [
        'appointment_code',
        'cart_id',
        'user_id',
        'service_id',
        'date',
        'time',
        'employee_id',
        'total',
        'status',
        'cancellation_reason',
        'notes',
        'first_name',
        'payment',
        'last_four_digits',

    ];

    protected $casts = [
        'first_name' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }





    static function boot()
    {
        parent::boot();

        static::creating(function ($appointment) {
            // a readable unique code for the appointment, including the id in the code
            $appointment->appointment_code = 'APP-'.  ($appointment->count() + 1) ;

        });
    }


}
