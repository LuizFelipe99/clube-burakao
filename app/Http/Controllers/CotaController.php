<?php

namespace App\Http\Controllers;

use App\Models\Cota;
use Illuminate\Http\Request;

class CotaController extends Controller
{

    public function store(Request $request)
    {
        //
        $request->validate([
        'nome' => 'required|string',
        'valor' => 'required|numeric'
    ]);

    $cota = Cota::create($request->all());

    return response()->json($cota, 201);
    }

   
}
