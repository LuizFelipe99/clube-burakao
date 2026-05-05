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

       // 👇 pai
    public function parent()
    {
        return $this->belongsTo(Cota::class, 'parent_id');
    }

    // 👇 filhos (subcotas)
    public function subcotas()
    {
        return $this->hasMany(Cota::class, 'parent_id');
    }
    
    
}
