<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Associado;

class AssociadoCotaController extends Controller
{
    public function vincular(Request $request, $associadoId)
    {
        $request->validate([
            'cota_id' => 'required|exists:cotas,id'
        ]);

        $associado = Associado::findOrFail($associadoId);

        $associado->cotas()->attach($request->cota_id, [
            'data_inicio' => now()
        ]);

        return response()->json([
            'message' => 'Cota vinculada com sucesso'
        ]);
    }
}
