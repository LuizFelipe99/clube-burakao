<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Associado;
use App\Models\Cota;

class AssociadoCotaController extends Controller
{
    public function vincular(Request $request, $associadoId)
    {
        $request->validate([
            'cota_id' => 'required|exists:cotas,id'
        ]);

        $associado = Associado::findOrFail($associadoId);
        $cota = Cota::findOrFail($request->cota_id);

        // 🚫 impedir duplicidade
        if ($associado->cotas()->where('cota_id', $cota->id)->exists()) {
            return response()->json([
                'message' => 'Associado já vinculado a essa cota'
            ], 400);
        }

        // 🎯 SE FOR COTA PAI (titular)
        if (is_null($cota->parent_id)) {

            // já existe titular?
            $jaTemTitular = Associado::whereHas('cotas', function ($q) use ($cota) {
                $q->where('cota_id', $cota->id);
            })->exists();

            if ($jaTemTitular) {
                return response()->json([
                    'message' => 'Essa cota já possui um titular'
                ], 400);
            }

            // esse associado já é dependente de alguma subcota dessa cota?
            $subcotasIds = $cota->subcotas()->pluck('id');

            $jaEhDependente = $associado->cotas()
                ->whereIn('cota_id', $subcotasIds)
                ->exists();

            if ($jaEhDependente) {
                return response()->json([
                    'message' => 'Associado já é dependente dessa cota'
                ], 400);
            }
        }

        // 🎯 SE FOR SUBCOTA (dependente)
        else {

            // já é titular da cota pai?
            $jaEhTitular = $associado->cotas()
                ->where('cota_id', $cota->parent_id)
                ->exists();

            if ($jaEhTitular) {
                return response()->json([
                    'message' => 'Titular não pode ser dependente'
                ], 400);
            }
        }

        // ✅ cria vínculo
        $associado->cotas()->attach($cota->id, [
            'data_inicio' => now()
        ]);

        return response()->json([
            'message' => 'Vínculo criado com sucesso'
        ]);
    }
}
