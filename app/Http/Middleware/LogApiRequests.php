<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Profile;


class LogApiRequests
{
    public function handle($request, Closure $next)
    {

        if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('delete')) {

        $user = Auth::user(); // Obtém o usuário autenticado (se houver)
        if(  $user){
            $profile = Profile::find($user->id);
           $dados =  $user->name.' ID-> '.$user->id.' Perfil '.$profile  ;
        }

        // Registre as informações que você deseja no log
        Log::channel('custom_api_log')->info("Requisição API: Método - {$request->method()}, Rota - {$request->path()}, Usuário - " . ($user ? $dados: 'Não autenticado'));

    }

    return $next($request);

}



}