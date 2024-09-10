<?php

namespace App\Http\Controllers;
use App\Models\Visit;
use App\Models\User;
use App\Models\Warning;
use App\Models\Wall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getLastOnlineUsers()
    {
        // Busca os últimos 5 usuários online com base na última visita
        $onlineUsers = DB::table('visits')
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
    $totalVisits = DB::table('visits')->count();

    // Agrupa as visitas diárias para os últimos 5 dias
    $accessStats = DB::table('visits')
        ->selectRaw('DATE(visited_at) as date, count(*) as total_visits')
        ->groupBy('date')
        ->orderBy('date', 'desc')
        ->take(5)
        ->get();

    // Obtém o total de usuários, avisos e ocorrências
    $totalUsers = User::count();
    $totalWarnings = Warning::count();// Ajuste conforme a tabela de ocorrências
    $totalAvisos = Wall::count(); 

    // Obtém as 10 páginas mais visitadas
    $mostVisitedPages = DB::table('visits')
        ->select('url', DB::raw('count(*) as visit_count'))
        ->groupBy('url')
        ->orderBy('visit_count', 'desc')
        ->take(10)
        ->get();

    return response()->json([
        'error' => '',
        'success' => true,
        'totalVisits' => $totalVisits, // Total geral de visitas
        'totalUsers' => $totalUsers, // Total de usuários
        'totalWarnings' => $totalWarnings, // Total de avisos
        'totalWalls' => $totalAvisos, // Total de ocorrências
        'dates' => $accessStats->pluck('date'),
        'counts' => $accessStats->pluck('total_visits'),
        'mostVisitedPages' => $mostVisitedPages // 10 páginas mais visitadas
    ], 200);
}
}
