<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Configuracao extends Model
{
    protected $table = 'configuracoes';

    protected $fillable = [
        'ano',
        'dias_semana'
    ];

    protected $casts = [
        'dias_semana' => 'array', // Converte JSON automaticamente para array
    ];
}
