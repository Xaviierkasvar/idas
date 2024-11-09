<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentTracking extends Model
{
    use HasFactory;

    // Definir la tabla si el nombre no sigue la convención de plural
    protected $table = 'payment_tracking';

    // Campos que se pueden asignar en masa
    protected $fillable = [
        'user_id',
        'associated_id',
        'bet_id',
        'draw_number',
        'bet_amount',
        'payout_amount',
        'transaction_type',
        'balance',
        'transaction_date',
    ];

    // Relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación con el modelo User (asociado)
    public function associatedUser()
    {
        return $this->belongsTo(User::class, 'associated_id');
    }

    // Relación con el modelo Bet
    public function bet()
    {
        return $this->belongsTo(Bet::class);
    }
}
