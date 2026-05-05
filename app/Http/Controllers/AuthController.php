<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
  public function register(Request $request)
  {
      $request->validate([
          'name' => 'required|string',
          'email' => 'required|email|unique:users,email',
          'password' => 'required|min:6'
      ]);

      $user = User::create([
          'name' => $request->name,
          'email' => $request->email,
          'password' => $request->password, // cast hashed resolve
      ]);

      $token = $user->createToken('auth_token')->plainTextToken;

      return response()->json([
          'user' => $user,
          'token' => $token
      ]);
  }

  public function login(Request $request)
  {
      // 1. Validar dados
      $request->validate([
          'email' => 'required|email',
          'password' => 'required'
      ]);

      // 2. Buscar usuário
      $user = User::where('email', $request->email)->first();

      // 3. Verificar credenciais
      if (!$user || !Hash::check($request->password, $user->password)) {
          return response()->json([
              'message' => 'Credenciais inválidas'
          ], 401);
      }

      // 4. (Opcional) remover tokens antigos
      $user->tokens()->delete();

      // 5. Gerar token
      $token = $user->createToken('auth_token')->plainTextToken;

      // 6. Retorno
      return response()->json([
          'user' => $user,
          'token' => $token
      ]);
  }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout realizado']);
    }
}
