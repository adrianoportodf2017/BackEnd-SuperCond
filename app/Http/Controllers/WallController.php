<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wall;
use App\Models\WallLike;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Exception;

/**
 * Classe responsável por gerenciar os avisos.
 */
class WallController extends Controller
{

    /**
     * Recupera todos os avisos.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAll()
    {
        // Buscar todos os avisos
        $walls = Wall::orderBy('created_at', 'desc')->get();    
        // Retornar uma mensagem de erro se não houver avisos
        if (!$walls) {
            return response()->json([
                'error' => 'Nenhum aviso encontrado',
                'code' => 404,
            ], 404);
        }
    
        // Adicionar as propriedades `likes` e `liked` a cada aviso
        $user = auth()->user();
        foreach ($walls as $wallKey => $wallValue) {
            $walls[$wallKey]['likes'] = WallLike::where('id_wall', $wallValue['id'])->count();
            $meLikes = WallLike::where('id_wall', $wallValue['id'])
                ->where('id_user', $user['id'])
                ->count();
            $walls[$wallKey]['liked'] = $meLikes > 0;
        }
    
        // Retornar uma resposta de sucesso com a lista de avisos
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $walls,
        ], 200);
    }


    public function getAllPublic()
    {
        // Buscar todos os avisos
        $walls = Wall::orderBy('created_at', 'desc')->get();    
        // Retornar uma mensagem de erro se não houver avisos
        if (!$walls) {
            return response()->json([
                'error' => 'Nenhum aviso encontrado',
                'code' => 404,
            ], 404);
        }
            // Retornar uma resposta de sucesso com a lista de avisos
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $walls,
        ], 200);
    }

    
    public function getAllEventsPublic()
{
    // Buscar todos os avisos onde o status é diferente de 1 e date_event não é nulo
    // Buscar todos os avisos onde o status é diferente de 1 e date_event não é nulo
    $walls = Wall::where('status', 1)
                 ->whereNotNull('date_event')
                 ->select('id', 'title', 'date_event as start', 'url_externa as url', 'content as description')
                 ->get()
                 ->map(function($wall) {
                     // Se a url for nula, criar uma URL personalizada
                     if (is_null($wall->url)) {
                         $wall->url = '/comunicados/' . $wall->id;
                     }
                     return $wall;
                 });



    // Retornar uma mensagem de erro se não houver avisos
    if ($walls->isEmpty()) {
        return response()->json([
            'error' => 'Nenhum aviso encontrado',
            'code' => 404,
        ], 404);
    }

    // Retornar uma resposta de sucesso com a lista de avisos
    return response()->json([
        'error' => '',
        'success' => true,
        'list' => $walls,
    ], 200);
}
    
    /**
     * Curte ou descurte um aviso.
     *
     * @param int $id O ID do aviso a ser curtido ou descurtido.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function like($id)
    {
        // Buscar o aviso a ser curtido ou descurtido
        $wall = Wall::find($id);
    
        // Retornar uma mensagem de erro se o aviso não for encontrado
        if (!$wall) {
            return response()->json([
                'error' => 'Aviso não encontrado',
                'code' => 404,
            ], 404);
        }
    
        // Buscar o usuário autenticado
        $user = auth()->user();
    
        // Verificar se o usuário já curtiu o aviso
        $meLikes = WallLike::where('id_wall', $id)
            ->where('id_user', $user['id'])
            ->count();
    
        // Se o usuário já curtiu o aviso, descurtir
        if ($meLikes > 0) {
            try {
                WallLike::where('id_wall', $id)
                    ->where('id_user', $user['id'])
                    ->delete();
            } catch (Exception $e) {
                // Tratar o erro
                return response()->json([
                    'error' => 'Erro ao descurtir aviso!',
                    'detail' => $e->getMessage(),
                    'code' => 500,
                ], 500);
            }
    
            // Retornar uma resposta de sucesso com o aviso descurtido
            return response()->json([
                'success' => true,
                'liked' => false,
                'likes' => WallLike::where('id_wall', $id)->count(),
            ], 200);
        }
    
        // Se o usuário ainda não curtiu o aviso, curtir
        try {
            $newLike = new WallLike();
            $newLike->id_wall = $id;
            $newLike->id_user = $user['id'];
            $newLike->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao curtir aviso!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    
        // Retornar uma resposta de sucesso com o aviso curtido
        return response()->json([
            'error' => '',
            'success' => true,
            'liked' => true,
            'likes' => WallLike::where('id_wall', $id)->count(),
        ], 200);
    }
    

    /**
     * Recupera um aviso pelo seu ID.
     *
     * @param int $id O ID do aviso a ser recuperado.
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Se o aviso não for encontrado.
     */
    public function getById($id)
    {
        $wall = Wall::where('id', $id)->first();

        if (!$wall) {
            return response()->json([
                'error' => "Aviso com ID {$id} não encontrado",
                'code' => 404,
            ], 404);
        }

        return response()->json([
            'error' => '',
            'list' => $wall,
            // Outros dados de resultado aqui...
        ], 200);
    }



    /**
     * Insere um novo aviso.
     *
     * @param Request $request A requisição HTTP.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function insert(Request $request)
    {
        
        $newWall = new Wall();

        // Validar os dados da solicitação
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            'content' => 'required',
            'thumb' => 'mimes:jpg,png,jpeg',

        ]);

        // Retornar uma mensagem de erro se a validação falhar
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }
        // Criar um novo documento
        // Verificar se o arquivo é válido
        if ($request->file('thumb') && !$request->file('thumb')->isValid()) {
            return response()->json([
                'error' => 'O arquivo enviado não é válido',
                'code' => 400,
            ], 400);
        }
        if ($request->hasfile('thumb')) {
            // Salvar o arquivo no armazenamento
            $arquivo = $request->file('thumb')->store('public/wall/thumb');
            $url = asset(Storage::url($arquivo));
        } else {
            $arquivo = '';
            $url = '';
        }

        $newWall->title = $request->input('title');
        $newWall->content = $request->input('content');
        $newWall->thumb = $url;
        $newWall->thumb_file = $arquivo;
        $newWall->status = $request->input('status');
        $newWall->date_event = $request->input('date_event');
        $newWall->url_externa = $request->input('url_externa');



        // Salvar o objeto Wall no banco de dados
        try {
            $newWall->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar aviso!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        // Retornar uma resposta de sucesso
        return response()->json([
            'error' => '',
            'success' => true,
            // Outros dados de resultado aqui...
        ], 200);
    }


        /**
     * Atualiza um aviso.
     *
     * @param int $id O ID do aviso a ser atualizado.
     * @param Request $request A requisição HTTP.
     *
     * @return \Illuminate\Http\JsonResponse
     * */

     public function update($id, Request $request)
     {
        $array['id'] =  $id;
        // Buscar o documento pelo ID
        $wall = Wall::find($id);
        $arquivo = $wall->thumb_file;
        $url =  $wall->thumb;



        // Se o aviso não for encontrado, retornar uma mensagem de erro
        if (!$wall) {
            return response()->json([
                'error' => 'Aviso inexistente',
                'code' => 404,
            ], 404);
        }

        // Validar os dados da requisição
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'code' => 400,
            ], 400);
        }

        if ($request->file('thumb')) {
            // Validar os dados da requisição
            $validator = Validator::make($request->all(), [
                'thumb' => 'mimes:jpg,png,jpeg',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'O arquivo enviado não é válido',
                    'code' => 400,
                ], 400);
            }

            // Salvar o arquivo no armazenamento
            $arquivo = $request->file('thumb')->store('public/wall/thumb');
            $url = asset(Storage::url($arquivo));
            $thumb_delete = $wall->thumb_file;
            Storage::delete($thumb_delete);
        }


        $wall->title = $request->input('title');
        $wall->content = $request->input('content');
        $wall->thumb_file = $arquivo;
        $wall->thumb = $url;
        $wall->status = $request->input('status');
        $wall->type = $request->input('type');
        $wall->date_event = $request->input('date_event');
        $wall->url_externa = $request->input('url_externa');

        // Salvar o documento no banco de dados
        try {
            $wall->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Novo item!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

 
         // Retornar uma resposta de sucesso
         return response()->json([
            'error' => '',
             'success' => true
         ]);
     }


     public function updateStatus($id, Request $request)
     {
         $array = ['error' => ''];
         $validator = Validator::make($request->all(), [
             'status' => 'required',
         ]);
         if ($validator->fails()) {
             $array['error'] = $validator->errors()->first();
             return $array;
         } else {
             $item = Wall::find($id);
             $item->status = $request->input('status');
              // Salvar o documento no banco de dados
              try {
                 $item->save();
             } catch (Exception $e) {
                 // Tratar o erro
                 return response()->json([
                     'error' => 'Erro ao salvar Aviso!',
                     'detail' => $e->getMessage(),
                     'code' => 500,
                 ], 500);
             }
     
             // Retornar uma resposta de sucesso com os dados da notícia
             return response()->json([
                 'error' => '',
                 'success' => true,
                 'list' => $item,
             ], 201);
         }
 
     }
    /**
     * Exclui um aviso.
     *
     * @param int $id O ID do aviso a ser excluído.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id)
    {
        // Buscar o aviso a ser deletado
        $wall = Wall::find($id);

        // Se o aviso não for encontrado, retornar uma mensagem de erro
        if (!$wall) {
            return response()->json([
                'error' => 'Aviso inexistente',
                'code' => 404,
            ], 404);
        }

        // Tentar deletar o aviso
        try {
            $wall->delete();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao deletar aviso!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        // Retornar uma resposta de sucesso
        return response()->json([
            'error' => '',
            'success' => true,
        ], 200);
    }
}
