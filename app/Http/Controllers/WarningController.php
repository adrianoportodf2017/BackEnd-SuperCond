<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warning;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Exception;



class WarningController extends Controller
{

    /**
     * Obtém todos os documentos.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAll()
    {
        $warning =  Warning::all();

        // Retornar uma mensagem de erro se não houver ocorrencias
        if (!$warning) {
            return response()->json([
                'error' => 'Nenhum aviso encontrado',
                'code' => 404,
            ], 404);
        }
        // Retornar uma resposta de sucesso com a lista de ocorrencias
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => json_decode($warning),
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
        $wall = Doc::where('id', $id)->first();

        if (!$wall) {
            return response()->json([
                'error' => "Documento com ID {$id} não encontrado",
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
     * Insere um novo documento.
     *
     * @param \Illuminate\Http\Request $request Os dados do documento a ser inserido.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function insert(Request $request)
    {
        // Validar os dados da requisição
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            'file' => 'required|mimes:jpg,png,pdf,jpeg',
        ]);

        // Retornar uma mensagem de erro se a validação falhar
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }

        // Verificar se o arquivo existe
        if (!$request->hasfile('file')) {
            return response()->json([
                'error' => 'Nenhum arquivo enviado',
                'code' => 400,
            ], 400);
        }

        // Verificar se o arquivo é válido
        if (!$request->file('file')->isValid()) {
            return response()->json([
                'error' => 'O arquivo enviado não é válido',
                'code' => 400,
            ], 400);
        }

        // Salvar o arquivo no armazenamento
        $arquivo = $request->file('file')->store('public');
        $url = asset(Storage::url($arquivo));

        // Criar um novo documento
        $newDoc = new Doc();
        $newDoc->title = $request->input('title');
        $newDoc->content = $request->input('content');
        $newDoc->category_id = $request->input('category_id');
        $newDoc->fileurl = $url;
        $newDoc->filename = $arquivo;

        // Salvar o documento no banco de dados
        try {
            $newDoc->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar documento!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        // Retornar uma resposta de sucesso
        return response()->json([
            'error' => '',
            'success' => true,
            'document' => $newDoc,
        ], 201);
    }
    public function update($id, Request $request)
    {
        $array['id'] =  $id;
        // Buscar o documento pelo ID
        $doc = Doc::find($id);

        // return var_dump($_POST);die;

        // Retornar uma mensagem de erro se o documento não for encontrado
        if (!$doc) {
            return response()->json([
                'error' => 'Documento não encontrado',
                'code' => 404,
            ], 404);
        }
        if ($request->hasfile('file')) {
            $validator = Validator::make($request->all(), [
                'title' => 'required|min:2',
                'file' => 'required|mimes:jpg,png,pdf,jpeg'
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()->first(),
                    'code' => 400,
                ], 400);
            } else {

                $title = $request->input('title');
                $content = $request->input('content');
                $category_id = $request->input('category_id');
                $arquivo = $request->file('file')->store('public');
                $url = asset(Storage::url($arquivo));
                if ($doc) {
                    $fileDelete = $doc->filename;
                    $doc->title = $title;
                    $doc->content = $content;
                    $doc->category_id = $category_id;
                    $doc->fileurl = $url;
                    $doc->filename = $arquivo;
                    $array['error'] = '';
                    // Salvar o documento no banco de dados
                    try {
                        Storage::delete($fileDelete);
                        $doc->save();
                    } catch (Exception $e) {
                        // Tratar o erro
                        return response()->json([
                            'error' => 'Erro ao salvar documento!',
                            'detail' => $e->getMessage(),
                            'code' => 500,
                        ], 500);
                    }
                }
            }
        } else {
            $validator = Validator::make($request->all(), [
                'title' => 'required|min:2',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()->first(),
                    'code' => 400,
                ], 400);
            } else {

                $title = $request->input('title');
                $content = $request->input('content');
                $category_id = $request->input('category_id');
                if ($doc) {
                    $doc->title = $title;
                    $doc->content = $content;
                    $doc->category_id = $category_id;
                    // Salvar o documento no banco de dados
                    try {
                        $doc->save();
                    } catch (Exception $e) {
                        // Tratar o erro
                        return response()->json([
                            'error' => 'Erro ao salvar documento!',
                            'detail' => $e->getMessage(),
                            'code' => 500,
                        ], 500);
                    }
                }
            }
        }
        // Retornar uma resposta de sucesso
        return response()->json([
            'error' => '',
            'success' => true,
            'document' => $doc,
        ], 200);
    }
    /**
     * Exclui um documento.
     *
     * @param int $id O ID do documento a ser excluído.
     *
     * @return \Illuminate\Http\JsonResponse 
     * */

    public function delete($id)
    {
        // Buscar o aviso a ser deletado
        $doc = Doc::find($id);

        // Se o aviso não for encontrado, retornar uma mensagem de erro
        if (!$doc) {
            return response()->json([
                'error' => 'Documento inexistente',
                'code' => 404,
            ], 404);
        }

        // Tentar deletar o aviso
        try {
            $doc->delete();
            $fileDelete = $doc->filename;
            Storage::delete($fileDelete);
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao deletar documento!',
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
