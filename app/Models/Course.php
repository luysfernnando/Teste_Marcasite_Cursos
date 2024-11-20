<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'is_active',
        'start_date',
        'end_date',
        'remaining_slots'
    ];

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'registrations', 'course_id', 'user_id')
            ->withPivot('id', 'created_at', 'payment_status', 'paid_value')
            ->withTimestamps();
    }
}
