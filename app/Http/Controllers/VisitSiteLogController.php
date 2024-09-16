<?php

namespace App\Http\Controllers;
use App\Models\VisitSiteLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VisitSiteLogController extends Controller
{
    public function visits(Request $request)
    {
        

        // Registrar a visita
        $visit = VisitSiteLog::create([
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

    public function getLastOnlineUsers()
    {
        // Busca os últimos 5 usuários online com base na última visita
        $onlineUsers = DB::table('visits_site_log')
            ->join('users', 'visits.user_id', '=', 'users.id')
            ->select('users.id', 'users.name', DB::raw('MAX(visits.visited_at) as lastOnline'))
            ->whereNotNull('visits.user_id') // Apenas onde o user_id não é nulo
            ->groupBy('users.id', 'users.name') // Agrupa pelos campos do usuário
            ->orderBy('lastOnline', 'desc') // Ordena pela última visita
            ->take(5) // Limita para os últimos 5 usuários
            ->get();
    
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $onlineUsers,
        ], 200);
    }

public function getAccessStats()
{
    // Obtém o total geral de visitas
    $totalVisits = DB::table('visits_site_log')->count();

    // Agrupa as visitas diárias para os últimos 5 dias
    $accessStats = DB::table('visits_site_log')
        ->selectRaw('DATE(visited_at) as date, count(*) as total_visits')
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->take(5)
        ->get();

    // Estrutura semelhante ao que você espera
    $dates = $accessStats->pluck('date');
    $counts = $accessStats->pluck('total_visits');

    return response()->json([
        'error' => '',
        'success' => true,
        'totalVisits' => $totalVisits, // Total geral de visitas
        'dates' => $dates,
        'counts' => $counts


    ], 200);
}



}
