<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\News;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Exception;



class NewsController extends Controller
{
    public function getAll()
    {
        $array = ['error' => ''];
        $news = News::orderBy('created_at', 'desc')->get();
        if ($news->isEmpty()) {  // Usando isEmpty() para verificar se a coleção está vazia
            return response()->json([
                'error' => "Nenhuma notícia encontrada",
                'code' => 404,
            ], 404);
        }
        return response()->json([
            'error' => '',
            'list' => $news,
            // Outros dados de resultado aqui...
        ], 200);
    }

    public function getAllPublic()
    {
        $array = ['error' => ''];
        
        // Filtrar notícias com status igual a 1 e ordenar do mais novo para o mais antigo
        $news = News::where('status', 1)->orderBy('created_at', 'desc')->get();
    
        if ($news->isEmpty()) {
            return response()->json([
                'error' => "Nenhuma notícia encontrada",
                'code' => 404,
            ], 404);
        }
    
        return response()->json([
            'error' => '',
            'list' => $news,
            // Outros dados de resultado aqui...
        ], 200);
    }
    

      /**
     * Obtém um documento pelo ID.
     *
     * @param int $id O ID do documento a ser obtido.
     *
     * @return \App\Models\Doc
     */
    public function getById($id)
    {
        $new = News::where('id', $id)->first();
        if (!$new) {
            return response()->json([
                'error' => "Notícia com ID {$id} não encontrado",
                'code' => 404,
            ], 404);
        }
        return response()->json([
            'error' => '',
            'list' => $new,
            // Outros dados de resultado aqui...
        ], 200);
    }

    public function insert(Request $request)
    {
        $array = ['error' => ''];

        // Validar os dados da request
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            'content' => 'required|min:2',
            //'file' => 're            'year' => 'required',
            //'file' => 'required|mimes:jpg,png,pdf,jpeg'
        ]);

        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return response()->json($array, 400);
        }

        if ($request->hasfile('thumb')) {

            $validator = Validator::make($request->all(), [
                'thumb' => 'required|mimes:jpg,png,jpeg'
            ]);

            if ($request->file('thumb')->isValid()) {
                $arquivo = $request->file('thumb')->store('public/image/noticias');
                $url = asset(Storage::url($arquivo));
            } else {
                $array['error'] = $validator->errors()->first();
            }
        } else {
            $url  = '';
        }

        // Verificar se o slug já existe
        $slug = $request->input('slug');
        $news = News::where('slug', $slug)->first();

        if ($news) {
            $i = 1;
            while ($news) {
                $newSlug = $slug . '-' . $i;
                $news = News::where('slug', $newSlug)->first();
                $i++;
            }

            $slug = $newSlug;
        }

        // Criar uma nova notícia
        $new = new News();
        $new->title = $request->input('title');
        $new->content = $request->input('content');
        $new->slug = $slug;
        $new->thumb =  $url;
        $new->category_id = $request->input('category_id');
        $new->author_id = $request->input('author_id');
        $new->tags = $request->input('tags');
        $new->highlight = $request->input('highlight');
        $new->status = $request->input('status');

        // Salvar o documento no banco de dados
        try {
            $new->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Notícia!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        // Retornar uma resposta de sucesso com os dados da notícia
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $new,
        ], 201);
    }

    public function update($id, Request $request)
    {
        $array = ['error' => ''];
        $array['id'] =  $id;
        $new = News::find($id);
        if (!$new) {
            $array['error'] = 'Registro não encontrado';
            return $array;
        }
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            'content' => 'required|min:2',
       
        ]);

        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return response()->json($array, 400);
        }

        if ($request->hasfile('thumb')) {

            $validator = Validator::make($request->all(), [
                'thumb' => 'required|mimes:jpg,png,jpeg'
            ]);

            if ($request->file('thumb')->isValid()) {
                $arquivo = $request->file('thumb')->store('public/image/noticias');
                $url = asset(Storage::url($arquivo));
                $thumbDelete = $new->thumb;
                // Converta a URL em um caminho relativo ao sistema de arquivos
                $relativePath = str_replace(asset(''), '', $thumbDelete);
                $relativePath = str_replace('storage', '', $relativePath);
                Storage::delete('public' . $relativePath);
                $new->thumb =  $url;

            } else {
                $array['error'] = $validator->errors()->first();
            }
        }

        $new->title = $request->input('title');
        $new->content = $request->input('content');
        $new->category_id = $request->input('category_id');
        $new->author_id = $request->input('author_id');
        $new->tags = $request->input('tags');
        $new->highlight = $request->input('highlight');
        $new->status = $request->input('status');
          // Salvar o documento no banco de dados
          try {
            $new->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Notícia!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        // Retornar uma resposta de sucesso com os dados da notícia
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $new,
        ], 201);
    }

    public function delete($id)
    {
        $array = ['error' => ''];
    
        try {
            // Verificar se o documento existe
            $item = News::find($id);
    
            if (!$item) {
                return response()->json([
                    'error' => 'Documento inexistente',
                    'code' => 404,
                ], 404);
            }
    
            // Excluir a imagem de thumbnail
            if ($item->thumb) {
                $relativePath = str_replace(asset(''), '', $item->thumb);
                $relativePath = str_replace('storage', '', $relativePath);
                Storage::delete('public' . $relativePath);
            }
    
            // Excluir o documento do banco de dados
            $item->delete();
    
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Ocorreu um erro ao excluir o documento: ' . $e->getMessage(),
                'code' => 500,
            ], 500);
        }
        
            // Retornar uma resposta de sucesso
            return response()->json([
                'error' => '',
                'success' => true,
            ], 200);
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
            $item = News::find($id);
            $item->status = $request->input('status');
            $item->save();
            return $request->input();
        }
    }

    public function midias()
    {
        return $this->morphMany(Midia::class, 'mediable');
    } 
}
