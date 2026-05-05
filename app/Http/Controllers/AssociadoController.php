<?php

namespace App\Http\Controllers;

use App\Models\Cota;
use Illuminate\Http\Request;
use App\Models\Associado;

class AssociadoController extends Controller
{
    public function cotas()
    {
        return $this->belongsToMany(Cota::class, 'associado_cota')
            ->withPivot(['data_inicio', 'data_fim'])
            ->withTimestamps();
    }
        // get
    public function index()
    {
        return Associado::all();
    }
    
    //POST
    public function store(Request $request)
    {
        return Associado::create($request->all());
    }

    //PUT
    public function update(Request $request, Associado $associado)
    {
        $associado->update($request->all());
        return $associado;
    }
    
}
