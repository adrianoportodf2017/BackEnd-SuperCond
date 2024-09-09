<?php

namespace App\Http\Controllers;
use App\Models\Visit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisitController extends Controller
{
    public function visits(Request $request)
    {
        

        // Registrar a visita
        $visit = Visit::create([
            'user_id' => Auth::check() ? Auth::id() : null,  // Verifica se o usuário está logado
            'ip_address' => $request->ip(),
            'url' => $request->input('url'),
            'visited_at' => now(),
        ]);

        // Retornar uma resposta de sucesso
        return response()->json([
            'message' => 'Visita registrada com sucesso!',
            'data' => $visit
        ], 201);
    }
}
