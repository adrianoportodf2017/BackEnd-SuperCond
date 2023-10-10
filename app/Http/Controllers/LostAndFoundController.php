<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LostAndFound;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Exception;


class LostAndFoundController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getAll()
    {
        $lostAndFounds =  LostAndFound::all();

        // Retornar uma mensagem de erro se não houver ocorrencias
        if (!$lostAndFounds) {
            return response()->json([
                'error' => 'Nenhum Achado e perdido encontrado',
                'code' => 404,
            ], 404);
        }
        // Retornar uma resposta de sucesso com a lista de ocorrencias
        $result = [];
        foreach ($lostAndFounds as $lostAndFound) {
            $lostAndFound->photos_array = json_decode($lostAndFound->photos);
            $result[]['warnings'] = $lostAndFound;
        }

        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $result,
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
        $lostAndFound = LostAndFound::where('id', $id)->first();

        if (!$lostAndFound) {
            return response()->json([
                'error' => "Item com ID {$id} não encontrado",
                'code' => 404,
            ], 404);
        }
        $lostAndFound->photos_array = json_decode($lostAndFound->photos);
        return response()->json([
            'error' => '',
            'list' => json_decode($lostAndFound),
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
    {      //  return var_dump($request->file()); die;

        // Validar os dados da requisição
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            'file' =>  'required|max:2M',
            'file.*' => 'mimes:jpg,png,pdf,jpeg',
            'owner_id' => 'required',
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
        $files = $request->file('file');
        foreach ($files as $file) {
            if (!$file->isValid()) {
                return response()->json([
                    'error' => 'O arquivo enviado não é válido',
                    'code' => 400,
                ], 400);
            }
        }
        $cont = '0';
        $file = [];
        foreach ($files as  $key) {
            $arquivo = $key->store('public/lostAndFound/' . $request->input('owner_id'));
            $file[$cont] = $arquivo;
            $cont++;
        }
        $json_files = json_encode($file);

        // Criar um novo documento
        $newLostAndFound = new LostAndFound();
        $newLostAndFound->title = $request->input('title');
        $newLostAndFound->content = $request->input('content');
        $newLostAndFound->notes = $request->input('notes');
        $newLostAndFound->owner_id = $request->input('owner_id');
        $newLostAndFound->photos = $json_files;
        $newLostAndFound->status = 'Não Encontrado';


        // Salvar o documento no banco de dados
        try {
            $newLostAndFound->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Ocorrência!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        // Retornar uma resposta de sucesso
        return response()->json([
            'error' => '',
            'success' => true,
            'document' => $newLostAndFound,
        ], 201);
    }
    public function update($id, Request $request)
    {
        $array['id'] =  $id;
        // Buscar o documento pelo ID
        $lostAndFound = LostAndFound::find($id);
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            'owner_id' => 'required',

        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'code' => 400,
            ], 400);
        } else {

            if ($lostAndFound) {
                $lostAndFound->title = $request->input('title');
                $lostAndFound->content = $request->input('content');
                $lostAndFound->notes = $request->input('notes');
                $lostAndFound->owner_id = $request->input('owner_id');
                $lostAndFound->status = $request->input('status');
                // Salvar o documento no banco de dados
                try {
                    $lostAndFound->save();
                } catch (Exception $e) {
                    // Tratar o erro
                    return response()->json([
                        'error' => 'Erro ao salvar Ocorrência!',
                        'detail' => $e->getMessage(),
                        'code' => 500,
                    ], 500);
                }
            }
        }

        // Retornar uma resposta de sucesso
        return response()->json([
            'error' => '',
            'success' => true,
            'document' => $lostAndFound,
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
        $lostAndFound = LostAndFound::find($id);

        // Se o aviso não for encontrado, retornar uma mensagem de erro
        if (!$lostAndFound) {
            return response()->json([
                'error' => 'Ocorrência inexistente',
                'code' => 404,
            ], 404);
        }

        // Tentar deletar o aviso
        try {
            $lostAndFound->delete();
            $fileDelete = $lostAndFound->filename;
            $lostAndFound->photos_array = json_decode($lostAndFound->photos);
            foreach ($lostAndFound->photos_array as $photos) {
                Storage::delete($photos);
            }
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao deletar Achados e perdidos!',
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
