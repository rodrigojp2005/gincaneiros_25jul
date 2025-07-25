<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gincana extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'duracao',
        'latitude',
        'longitude',
        'contexto',
        'privacidade',
    ];

     public function locais()
    {
        return $this->hasMany(GincanaLocal::class);
    }
}
