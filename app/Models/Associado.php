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
}
