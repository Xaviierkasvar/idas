<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Bet extends Model
{
    use HasFactory;

    protected $table = 'bets';

    // El campo 'id' es la clave primaria autoincremental, y 'bet_id' agrupa varias apuestas
    protected $primaryKey = 'id';

    // Campos que pueden ser asignados en masa
    protected $fillable = [
        'bet_id',
        'draw_number',
        'bet_number',
        'bet_amount',
        'user_id',
        'associated_id',
        'bet_date_time',
    ];

    /**
     * Relación con el modelo User para el usuario que realiza la apuesta.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con el modelo User para el asociado o líder.
     */
    public function associated()
    {
        return $this->belongsTo(User::class, 'associated_id');
    }

    /**
     * Relación para obtener todas las apuestas que pertenecen al mismo 'bet_id'.
     */
    public function relatedBets()
    {
        return $this->hasMany(self::class, 'bet_id', 'bet_id');
    }

    public function getBetDateTimeAttribute($value)
    {
        return Carbon::parse($value);
    }
}
