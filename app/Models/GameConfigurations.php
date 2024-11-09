<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GameConfigurations extends Model
{
    use HasFactory;

    protected $fillable = [
        'draw_number',
        'draw_name',
        'restricted_numbers',
        'max_bet_amount',
        'max_repeats_per_number',
        'closing_time',
        'draw_date',
        'seller_margin',
        'is_active',
    ];

    protected $casts = [
        'restricted_numbers' => 'array',
        'draw_date' => 'date',
    ];

    /**
     * Actualiza el estado activo de la configuración del juego.
     */
    public function updateActiveStatus()
    {
        $now = Carbon::now();

        $closingTime = Carbon::parse($this->closing_time);

        if ($now->greaterThanOrEqualTo($closingTime)) {
            $this->is_active = false;
            $this->save();
        }
    }
}
