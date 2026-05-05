<?php

namespace App\Http\Controllers;

use App\Models\Cota;
use Illuminate\Http\Request;
use App\Models\Associado;

class CotaController extends Controller
{
  public function store(Request $request)
  {
    $request->validate([
      'nome' => 'required|string',
      'valor' => 'required|numeric',
      'parent_id' => 'nullable|exists:cotas,id',
    ]);

    $cota = Cota::create($request->all());

    return response()->json($cota, 201);

  }

  // listar somente subcotas
  public function subcotas(Request $request)
  {
    // http://127.0.0.1:8000/api/subcotas?ativo=1&nome=depend&parent_id=1
    $query = Cota::whereNotNull('parent_id');

    //  filtrar por cota pai
    if ($request->filled('parent_id')) {
      $query->where('parent_id', $request->parent_id);
    }

    //  filtrar por nome
    if ($request->filled('nome')) {
      $query->where('nome', 'ILIKE', '%' . $request->nome . '%'); // Postgres
    }

    //  filtrar por ativo
    if ($request->filled('ativo')) {
      $query->where('ativo', $request->ativo);
    }

    $subcotas = $query->paginate(10);

    // return response()->json($subcotas, 200);

    return response()->json([
      'data' => $subcotas->items(),
      'meta' => [
        'current_page' => $subcotas->currentPage(),
        'last_page' => $subcotas->lastPage(),
        'per_page' => $subcotas->perPage(),
        'total' => $subcotas->total(),
      ],
    ]);
  }

public function associados($id)
{
    $cota = \App\Models\Cota::with('subcotas')->findOrFail($id);

    // IDs da cota + subcotas
    $cotasIds = $cota->subcotas->pluck('id')->push($cota->id);

    // busca associados únicos
    $associados = \App\Models\Associado::whereHas('cotas', function ($q) use ($cotasIds) {
        $q->whereIn('cota_id', $cotasIds);
    })
    ->with(['cotas' => function ($q) use ($cotasIds) {
        $q->whereIn('cota_id', $cotasIds);
    }])
    ->get()
    ->unique('id') // remove duplicados
    ->values();

    // titular = quem está na cota principal
    $titular = $associados->first(function ($a) use ($cota) {
        return $a->cotas->contains('id', $cota->id);
    });

    // dependentes = quem está nas subcotas
    $dependentes = $associados->filter(function ($a) use ($cota) {
        return $a->cotas->where('parent_id', $cota->id)->count();
    })->values();

    return response()->json([
        'cota' => $cota->nome,
        'titular' => $titular ? $titular->nome : null,
        'dependentes' => $dependentes->pluck('nome')
    ]);
}

public function adicionarDependente(Request $request, $cotaId)
{
    $request->validate([
        'dependente_id' => 'required|exists:associados,id'
    ]);

    $cota = Cota::with('subcotas')->findOrFail($cotaId);
    $dependente = Associado::findOrFail($request->dependente_id);

    // 🚫 já é titular?
    $jaEhTitular = $dependente->cotas()
        ->where('cota_id', $cota->id)
        ->exists();

    if ($jaEhTitular) {
        return response()->json([
            'message' => 'Titular não pode ser dependente'
        ], 400);
    }

    // 🚫 já é dependente dessa cota?
    $subcotasIds = $cota->subcotas->pluck('id');

    $jaEhDependente = $dependente->cotas()
        ->whereIn('cota_id', $subcotasIds)
        ->exists();

    if ($jaEhDependente) {
        return response()->json([
            'message' => 'Associado já é dependente dessa cota'
        ], 400);
    }

    // 🎯 pegar ou criar subcota automaticamente
    $subcota = $cota->subcotas()->first();

    if (!$subcota) {
        $subcota = Cota::create([
            'nome' => 'Dependente',
            'valor' => 0,
            'parent_id' => $cota->id
        ]);
    }

    // ✅ vincular dependente
    $dependente->cotas()->attach($subcota->id, [
        'data_inicio' => now()
    ]);

    return response()->json([
        'message' => 'Dependente adicionado com sucesso'
    ]);
}
}
