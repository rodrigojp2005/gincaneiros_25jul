<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Participacao extends Model
{
    protected $table = 'participacoes';
    
    protected $fillable = [
        'user_id',
        'gincana_id',
        'pontuacao',
        'inicio_participacao',
        'fim_participacao',
        'tempo_total_segundos',
        'status',
        'locais_visitados'
    ];

    protected $casts = [
        'inicio_participacao' => 'datetime',
        'fim_participacao' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function gincana(): BelongsTo
    {
        return $this->belongsTo(Gincana::class);
    }

    // Método para calcular tempo formatado
    public function getTempoFormatadoAttribute(): string
    {
        if (!$this->tempo_total_segundos) {
            return 'N/A';
        }

        $horas = floor($this->tempo_total_segundos / 3600);
        $minutos = floor(($this->tempo_total_segundos % 3600) / 60);
        $segundos = $this->tempo_total_segundos % 60;

        if ($horas > 0) {
            return sprintf('%02d:%02d:%02d', $horas, $minutos, $segundos);
        }
        return sprintf('%02d:%02d', $minutos, $segundos);
    }
}
