<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pages;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Exception;



class PagesController extends Controller
{
    public function getAll()
    {
        $array = ['error' => ''];
        $pages = Pages::all();
        if (!$pages) {
            return response()->json([
                'error' => "Nenhuma página não encontrado",
                'code' => 404,
            ], 404);
        }
        return response()->json([
            'error' => '',
            'list' => $pages,
            // Outros dados de resultado aqui...
        ], 200);
    }

    public function getAllPublic()
    {
        $array = ['error' => ''];
        $pages = Pages::where('status', 1)
            ->where('public_area', 1)
            ->orderBy('order')
            ->orderBy('title')
            ->get();

        if ($pages->isEmpty()) {
            return response()->json([
                'error' => "Nenhuma página encontrada",
                'code' => 404,
            ], 404);
        }

        return response()->json([
            'error' => '',
            'list' => $pages,
            // Outros dados de resultado aqui...
        ], 200);
    }

    public function getAllPublicAndMenu()
    {
        $array = ['error' => ''];
        $pages = Pages::where('status', 1)
                      ->where('public_area', 1)
                      ->where('main_menu', 1)
                      ->orderBy('order')
                      ->get();

        if ($pages->isEmpty()) {
            return response()->json([
                'error' => "Nenhuma página encontrada",
                'code' => 404,
            ], 404);
        }

        return response()->json([
            'error' => '',
            'list' => $pages,
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
        $page = Pages::where('id', $id)->first();
        if (!$page) {
            return response()->json([
                'error' => "Página com ID {$id} não encontrado",
                'code' => 404,
            ], 404);
        }
        return response()->json([
            'error' => '',
            'list' => $page,
            // Outros dados de resultado aqui...
        ], 200);
    }


    public function getBySlugPublic($slug)
    {
        $page = Pages::where('status', 1)
            ->where('public_area', 1)
            ->where('slug', $slug)
            ->first();

        if (!$page) {
            return response()->json([
                'error' => "Página com o slug '{$slug}' não encontrada",
                'code' => 404,
            ], 404);
        }

        return response()->json([
            'error' => '',
            'list' => $page,
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
            'thumb' => 'mimes:jpg,png,jpeg'
        ]);

        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return response()->json($array, 400);
        }

        if ($request->hasfile('thumb')) {
            if ($request->file('thumb')->isValid()) {
                $arquivo = $request->file('thumb')->store('public/image/noticias');
                $url = asset(Storage::url($arquivo));
            } else {
                $array['error'] = 'Invalid file upload';
                return response()->json($array, 400);
            }
        } else {
            $url = '';
        }

        // Gerar o slug a partir do título
        $slug = Str::slug($request->input('title'));

        // Verificar se o slug já existe
        $existingPage = Pages::where('slug', $slug)->first();
        if ($existingPage) {
            $i = 1;
            $baseSlug = $slug;
            while ($existingPage) {
                $slug = $baseSlug . '-' . $i;
                $existingPage = Pages::where('slug', $slug)->first();
                $i++;
            }
        }

        // Criar uma nova página
        $pages = new Pages();
        $pages->title = $request->input('title');
        $pages->content = $request->input('content');
        $pages->slug = $slug;
        $pages->thumb = $url;
        $pages->category_id = $request->input('category_id');
        $pages->author_id = $request->input('author_id');
        $pages->tags = $request->input('tags');
        $pages->highlight = $request->input('highlight');
        $pages->status = $request->input('status');
        $pages->order = $request->input('order');

        // Salvar a página no banco de dados
        try {
            $pages->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Notícia!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        // Retornar uma resposta de sucesso com os dados da página
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $pages,
        ], 201);
    }

    public function update($id, Request $request)
    {
        $array = ['error' => ''];
        $array['id'] = $id;
        $page = Pages::find($id);

        if (!$page) {
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
                $thumbDelete = $page->thumb;

                // Converta a URL em um caminho relativo ao sistema de arquivos
                $relativePath = str_replace(asset(''), '', $thumbDelete);
                $relativePath = str_replace('storage', '', $relativePath);
                Storage::delete('public' . $relativePath);

                $page->thumb = $url;
            } else {
                $array['error'] = $validator->errors()->first();
                return response()->json($array, 400);
            }
        }

        // Gerar o slug a partir do título
        $slug = Str::slug($request->input('title'));

        // Verificar se o slug já existe e não é o atual
        $existingPage = Pages::where('slug', $slug)->where('id', '!=', $id)->first();
        if ($existingPage) {
            $i = 1;
            $baseSlug = $slug;
            while ($existingPage) {
                $slug = $baseSlug . '-' . $i;
                $existingPage = Pages::where('slug', $slug)->where('id', '!=', $id)->first();
                $i++;
            }
        }

        $page->title = $request->input('title');
        $page->content = $request->input('content');
        $page->slug = $slug;
        $page->category_id = $request->input('category_id');
        $page->author_id = $request->input('author_id');
        $page->tags = $request->input('tags');
        $page->highlight = $request->input('highlight');
        $page->status = $request->input('status');
        $page->main_menu = $request->input('mainMenu');
        $page->restricted_area = $request->input('restrictedArea');
        $page->public_area = $request->input('publicArea');
        // Salvar a página no banco de dados
        try {
            $page->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Página!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        // Retornar uma resposta de sucesso com os dados da página
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $page,
        ], 201);
    }


    public function delete($id)
    {
        $array = ['error' => ''];

        try {
            // Verificar se o documento existe
            $item = Pages::find($id);

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
            $item = Pages::find($id);
            $item->status = $request->input('status');
            $item->save();
            return $request->input();
        }
    }
}
