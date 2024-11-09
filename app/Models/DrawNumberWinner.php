<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DrawNumberWinner extends Model
{
    use HasFactory;

    protected $table = 'draw_number_winner';

    protected $fillable = [
        'draw_number',
        'winning_number'
    ];

    public function gameConfiguration()
    {
        return $this->belongsTo(GameConfigurations::class, 'draw_number', 'draw_number');
    }
}
