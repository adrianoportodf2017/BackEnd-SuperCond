<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocsCategory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Exception;



class DocsCategoryController extends Controller
{
    public function getAll($type = NULL)
    {
        $array = ['error' => ''];

        // Consulta as categorias com base no tipo fornecido
        if ($type) {
            $categories = DocsCategory::where('type', $type)->get();

            $array['list'] = $categories;
        } else {
            $category = DocsCategory::all();
            $array['list'] = $category;
        }
        return $array;
    }

    public function insert(Request $request)
    {
        $array = ['error' => ''];

        // Validar os dados da request
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2',
            'type' => 'required|min:2',
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
                $arquivo = $request->file('thumb')->store('public/categorias/thumb');
                $url = asset(Storage::url($arquivo));
            } else {
                $array['error'] = $validator->errors()->first();
            }
        } else {
            $url  = '';
        }

        // Verificar se o slug já existe
        $slug = $request->input('name');
        // Remover espaços e acentos em uma linha
        $slug = preg_replace('/[^A-Za-z0-9]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $slug));
        // Converter para letras minúsculas (opcional)
        $slug = strtolower($slug);
        $categorySlug = DocsCategory::where('slug', $slug)->first();

        if ($categorySlug) {
            $i = 1;
            while ($categorySlug) {
                $newSlug = $slug . '-' . $i;
                $categorySlug = DocsCategory::where('slug', $newSlug)->first();
                $i++;
            }

            $slug = $newSlug;
        }

        // Criar uma nova notícia
        $categorie = new DocsCategory();
        $categorie->name = $request->input('name');
        $categorie->content = $request->input('content');
        $categorie->slug = $slug;
        $categorie->thumb =  $url;
        $categorie->type = $request->input('type');
        $categorie->status = $request->input('status');

        // Salvar o documento no banco de dados
        try {
            $categorie->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Categoria!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        // Retornar uma resposta de sucesso com os dados da notícia
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $categorie,
        ], 201);
    }

    public function update($id, Request $request)
    {
        $array = ['error' => ''];
        $array['id'] =  $id;
        $categorie = DocsCategory::find($id);
        if (!$categorie) {
            $array['error'] = 'Registro não encontrado';
            return $array;
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2',
            'type' => 'required|min:2',

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
                $arquivo = $request->file('thumb')->store('public/categorias/thumb');
                $url = asset(Storage::url($arquivo));
                $thumbDelete = $categorie->thumb;
                // Converta a URL em um caminho relativo ao sistema de arquivos
                $relativePath = str_replace(asset(''), '', $thumbDelete);
                $relativePath = str_replace('storage', '', $relativePath);
                Storage::delete('public' . $relativePath);
            } else {
                $array['error'] = $validator->errors()->first();
            }
        } else {
            $url  = '';
        }

        $slug = $request->input('name');
        // Remover espaços e acentos em uma linha
        $slug = preg_replace('/[^A-Za-z0-9]/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $slug));
        // Converter para letras minúsculas (opcional)
        $slug = strtolower($slug);
        $categorySlug = DocsCategory::where('slug', $slug)->first();

        if ($categorySlug) {
            $i = 1;
            while ($categorySlug) {
                $newSlug = $slug . '-' . $i;
                $categorySlug = DocsCategory::where('slug', $newSlug)->first();
                $i++;
            }

            $slug = $newSlug;
        }
        $categorie->name = $request->input('name');
        $categorie->content = $request->input('content');
        $categorie->thumb =  $url;
        $categorie->type = $request->input('type');
        $categorie->slug = $slug;
        $categorie->status = $request->input('status');
        // Verificar se o slug já existe

        // Salvar o documento no banco de dados
        try {
            $categorie->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Categoria!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        // Retornar uma resposta de sucesso com os dados da notícia
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $categorie,
        ], 201);
    }

    public function delete($id)
    {
        $array = ['error' => ''];

        try {
            // Verificar se o documento existe
            $item = DocsCategory::find($id);

            if (!$item) {
                return response()->json([
                    'error' => 'Categoria inexistente',
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
                'error' => 'Ocorreu um erro ao excluir a categoria: ' . $e->getMessage(),
                'code' => 500,
            ], 500);
        }
        
        return response()->json(['error' => '', 'success' => 'Categoria deletado com sucesso']);
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
            $item = DocsCategory::find($id);
            $item->status = $request->input('status');
            $item->save();
            return $request->input();
        }
    }
}
