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

    // Relacionamento para obter os registros de inscrição (registrations)
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    // Relacionamento para obter os usuários através das inscrições
    public function students()
    {
        return $this->belongsToMany(User::class, 'registrations', 'course_id', 'user_id')
            ->withPivot('id', 'created_at', 'payment_status', 'paid_value') // Pegando os campos extras da tabela registrations
            ->withTimestamps();
    }
}
