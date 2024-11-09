<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BetControl extends Model
{
    use HasFactory;

    // Definir el nombre de la tabla si no sigue la convención de Laravel
    protected $table = 'bet_controls';

    // Definir los campos que son asignables en masa
    protected $fillable = [
        'user_id',
        'associated_id',
        'draw_number',
        'total_bet_amount',
        'seller_margin',
        'house_margin',
    ];

    // Relaciones con otros modelos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function associatedUser()
    {
        return $this->belongsTo(User::class, 'associated_id');
    }
}
