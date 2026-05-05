<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Associado extends Model
{
    //
    protected $fillable = [
        'nome',
        'email',
        'telefone',
        'ativo'
    ];

    public function cotas()
    {
        return $this->belongsToMany(Cota::class, 'associado_cota')
            ->withPivot(['data_inicio', 'data_fim'])
            ->withTimestamps();
    }
}
