<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cota extends Model
{
    //
        protected $fillable = [
        'nome',
        'valor',
        'parent_id',
        'ativo'
    ];
    public function associados()
    {
        return $this->belongsToMany(Associado::class, 'associado_cota');
    }
    
    
}
