<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Escala extends Model
{
    use HasFactory;

    protected $fillable = ['data', 'porteiro1_id', 'porteiro2_id'];

    public function porteiro1() {
        return $this->belongsTo(Porteiro::class, 'porteiro1_id');
    }

    public function porteiro2() {
        return $this->belongsTo(Porteiro::class, 'porteiro2_id');
    }
}
