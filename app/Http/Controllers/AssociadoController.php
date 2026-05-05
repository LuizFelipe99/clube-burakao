<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;


use App\Models\Associado;

class AssociadoController extends Controller
{
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
