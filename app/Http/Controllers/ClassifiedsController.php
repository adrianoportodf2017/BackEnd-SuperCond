<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classified;
use App\Models\Midia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Exception;


class ClassifiedsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getAll()
    {
        $classifieds =  Classified::all();

        // Retornar uma mensagem de erro se não houver ocorrencias
        if (!$classifieds) {
            return response()->json([
                'error' => 'Nenhum Achado e perdido encontrado',
                'code' => 404,
            ], 404);
        }
        // Retornar uma resposta de sucesso com a lista de ocorrencias
        $result = [];
        foreach ($classifieds as $classified) {
            $classified->midias  = $classified->midias;;
            $result[] = $classified;
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
        $lostAndFound = Classified::where('id', $id)->first();

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

        $newClassifields = new Classified();


        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            'file' =>  'max:2M',
            'file.*' => 'mimes:jpg,png,jpeg',
            'user_id' => 'required',
            'price' => 'required',
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



        // Criar um novo documento
        $newClassifields->title = $request->input('title');
        $newClassifields->content = $request->input('content');
        $newClassifields->thumb = $request->input('thumb');
        $newClassifields->price = $request->input('price');
        $newClassifields->address = $request->input('address');
        $newClassifields->contact = $request->input('contact');
        $newClassifields->category_id = $request->input('category_id');
        $newClassifields->status = 'Não Vendido';


        // Salvar o documento no banco de dados
        try {
            $newClassifields->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Ocorrência!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        // Retornar uma resposta de sucesso



        $file = [];
        foreach ($files as  $key) {
            $arquivo = $key->store('public/classifieds/' . $request->input('user_id'));
            $midia = new Midia([
                'title' => 'Classificados',
                'content' => $arquivo,
                'status' => 'ativo', // Status da mídia
                'type' => 'imagem', // Tipo da mídia (por exemplo, imagem, PDF, etc.)
                'user_id' => $request->input('user_id')
            ]);
            // Associar a mídia a uma entidade (por exemplo, Document)
            $newClassifields->midias()->save($midia);
        }
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $newClassifields,
        ], 201);
    }
    public function update($id, Request $request)
    {
        $array['id'] =  $id;
        // Buscar o documento pelo ID
        $classified = Classified::find($id);

        // Validar os dados da requisição
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            'file' =>  'max:2M',
            'file.*' => 'mimes:jpg,png,jpeg',
            'user_id' => 'required',
            'price' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'code' => 400,
            ], 400);
        } else {

            if ($classified) {
                $classified->title = $request->input('title');
                $classified->content = $request->input('content');
                $classified->thumb = $request->input('thumb');
                $classified->price = $request->input('price');
                $classified->address = $request->input('address');
                $classified->contact = $request->input('contact');
                $classified->category_id = $request->input('category_id');
                $classified->status = 'Não Vendido';
                // Salvar o documento no banco de dados
                try {
                    $classified->save();
                } catch (Exception $e) {
                    // Tratar o erro
                    return response()->json([
                        'error' => 'Erro ao salvar Novo item!',
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
            'list' => $classified,
        ], 200);
    }

    public function insertMidia($id, Request $request)
    {
        $classified = Classified::find($id);

        // Se o aviso não for encontrado, retornar uma mensagem de erro
        if (!$classified) {
            return response()->json([
                'error' => 'Produto inexistente',
                'code' => 404,
            ], 404);
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
        foreach ($files as  $key) {
            $arquivo = $key->store('public/classifieds/' . $request->input('user_id'));
            $url = asset(Storage::url($arquivo));
            $midia = new Midia([
                'title' => 'Classificados',
                'url' => $url,
                'file' => $arquivo,
                'status' => 'ativo', // Status da mídia
                'type' => 'imagem', // Tipo da mídia (por exemplo, imagem, PDF, etc.)
                'user_id' => $request->input('user_id')
            ]);
            // Associar a mídia a uma entidade (por exemplo, Document)
            // Salvar o documento no banco de dados
            try {
                $classified->midias()->save($midia);
                
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
            'success' => true,
            'list' => $classified,
        ], 200);
        }
    }
    
    public function deleteMidia($id, Request $request)
    {
        // Buscar o aviso a ser deletado
        $midia = Midia::find($id);

        // Se o aviso não for encontrado, retornar uma mensagem de erro
        if (!$midia) {
            return response()->json([
                'error' => 'Arquivo inexistente',
                'code' => 404,
            ], 404);
        }

        // Tentar deletar o aviso
        try {
            $midia->delete();
            $midia = $midia->file;
            Storage::delete($midia);
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao deletar Arquivo!',
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
        $classified = Classified::find($id);

        // Se o aviso não for encontrado, retornar uma mensagem de erro
        if (!$classified) {
            return response()->json([
                'error' => 'Item inexistente',
                'code' => 404,
            ], 404);
        }

        // Tentar deletar o aviso
        try {
            $classified->delete();
            $fileDelete = $classified->filename;
            $classified->photos_array = json_decode($classified->photos);
            foreach ($classified->photos_array as $photos) {
                Storage::delete($photos);
            }
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao deletar Item do Classificado!',
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
