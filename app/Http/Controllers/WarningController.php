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
        $warnings =  Warning::all();

        // Retornar uma mensagem de erro se não houver ocorrencias
        if (!$warnings) {
            return response()->json([
                'error' => 'Nenhum aviso encontrado',
                'code' => 404,
            ], 404);
        }
        // Retornar uma resposta de sucesso com a lista de ocorrencias
        $result = [];
        foreach($warnings as $warning){
            $warning->photos_array = json_decode($warning->photos);
            $result[]['warnings'] = $warning;

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
        $warning = Warning::where('id', $id)->first();

        if (!$warning) {
            return response()->json([
                'error' => "Ocorrência com ID {$id} não encontrado",
                'code' => 404,
            ], 404);
        }
        $warning->photos_array = json_decode($warning->photos);
        return response()->json([
            'error' => '',
            'list' => json_decode($warning),
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
            'file' => 'required',
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
        foreach(  $files as  $key)
        {           
            $arquivo = $key->store('public/warnings/'.$request->input('owner_id'));
            $file[$cont] = $arquivo ;
            $cont++;
        }
        $json_files = json_encode($file);
      
        // Criar um novo documento
        $newWarning = new Warning();
        $newWarning->title = $request->input('title');
        $newWarning->content = $request->input('content');
        $newWarning->notes = $request->input('notes');
        $newWarning->owner_id = $request->input('owner_id');
        $newWarning->unit_id = $request->input('unit_id');
        $newWarning->photos = $json_files;
        $newWarning->status = 'Pendente';


        // Salvar o documento no banco de dados
        try {
            $newWarning->save();
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
            'document' => $newWarning,
        ], 201);
    }
    public function update($id, Request $request)
    {
        $array['id'] =  $id;
        // Buscar o documento pelo ID
        $warning = Warning::find($id);
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

                if ($warning) {
                    $warning->title = $request->input('title');
                    $warning->content = $request->input('content');
                    $warning->notes = $request->input('notes');
                    $warning->owner_id = $request->input('owner_id');
                    $warning->unit_id = $request->input('unit_id');
                    $warning->status = $request->input('status');
                    // Salvar o documento no banco de dados
                    try {
                        $warning->save();
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
            'document' => $warning,
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
        $warning = Warning::find($id);

        // Se o aviso não for encontrado, retornar uma mensagem de erro
        if (!$warning) {
            return response()->json([
                'error' => 'Ocorrência inexistente',
                'code' => 404,
            ], 404);
        }

        // Tentar deletar o aviso
        try {
            $warning->delete();
            $fileDelete = $warning->filename;
            $warning->photos_array = json_decode($warning->photos);
            foreach($warning->photos_array as $photos){
                Storage::delete($photos);
            }

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
