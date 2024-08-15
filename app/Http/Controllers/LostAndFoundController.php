<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LostAndFound;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Midia;

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
                'error' => 'Nenhuma Galeria Encontrado',
                'code' => 404,
            ], 404);
        }
        // Retornar uma resposta de sucesso com a lista de ocorrencias
        $result = [];

        $result = [];
        foreach ($lostAndFounds as $lostAndFound) {
            // Recupere a coleção de mídias
            $midias = $lostAndFound->midias;
            // Obtém a URL base para os ícones
            $iconBaseUrl = asset('assets/icons/');
            // Itere sobre cada item na coleção e adicione o tipo de arquivo e o ícone com URL completa
            foreach ($midias as $midia) {
                $fileExtension = strtolower(pathinfo($midia->url, PATHINFO_EXTENSION));
                if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $midia->type = 'imagem';
                    $midia->icon = $midia->url;
                } elseif ($fileExtension === 'pdf') {
                    $midia->type = 'pdf';
                    $midia->icon = $iconBaseUrl . '/pdf.png';
                } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                    $midia->type = 'word';
                    $midia->icon = $iconBaseUrl . '/word.png';
                } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
                    $midia->type = 'word';
                    $midia->icon = $iconBaseUrl . '/excel.png';
                } else {
                    $midia->type = 'outro';
                    $midia->icon = $iconBaseUrl . '/outros.png';
                }
            }
            $lostAndFound['midias'] = $midias;
            $result[] = $lostAndFound;
        }

        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $result,
        ], 200);
    }



    public function getAllUserId($userId)
    {
        $lostAndFounds =  LostAndFound::where('owner_id', $userId)->get();

        // Retornar uma mensagem de erro se não houver ocorrencias
        if (!$lostAndFounds) {
            return response()->json([
                'error' => 'Nenhuma achado Encontrado',
                'code' => 404,
            ], 404);
        }
        // Retornar uma resposta de sucesso com a lista de ocorrencias
        $result = [];

        $result = [];
        foreach ($lostAndFounds as $lostAndFound) {
            // Recupere a coleção de mídias
            $midias = $lostAndFound->midias;
            // Obtém a URL base para os ícones
            $iconBaseUrl = asset('assets/icons/');
            // Itere sobre cada item na coleção e adicione o tipo de arquivo e o ícone com URL completa
            foreach ($midias as $midia) {
                $fileExtension = strtolower(pathinfo($midia->url, PATHINFO_EXTENSION));
                if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
                    $midia->type = 'imagem';
                    $midia->icon = $midia->url;
                } elseif ($fileExtension === 'pdf') {
                    $midia->type = 'pdf';
                    $midia->icon = $iconBaseUrl . '/pdf.png';
                } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                    $midia->type = 'word';
                    $midia->icon = $iconBaseUrl . '/word.png';
                } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
                    $midia->type = 'word';
                    $midia->icon = $iconBaseUrl . '/excel.png';
                } else {
                    $midia->type = 'outro';
                    $midia->icon = $iconBaseUrl . '/outros.png';
                }
            }
            $lostAndFound['midias'] = $midias;
            $result[] = $lostAndFound;
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
        $lost = LostAndFound::where('id', $id)->first();

        if (!$lost) {
            return response()->json([
                'error' => "Ocorrência não encontrado",
                'code' => 404,
            ], 404);
        }
        $lost->midias = $lost->midias;
        return response()->json([
            'error' => '',
            'list' => $lost,
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
           // 'file' =>  'required|max:2M',
            //'file.*' => 'mimes:jpg,png,pdf,jpeg',
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
           // Criar um novo documento
        $newLostAndFound = new LostAndFound();
        $newLostAndFound->title = $request->input('title');
        $newLostAndFound->content = $request->input('content');
        $newLostAndFound->notes = $request->input('notes');
        $newLostAndFound->owner_id = $request->input('owner_id');
        $newLostAndFound->status = $request->input('status');


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
        if ($request->file('file')) {
            $files = $request->file('file');
            foreach ($files as  $key) {
                $arquivo = $key->store('public/lostAndFound/' . $newLostAndFound->id);
                $url = asset(Storage::url($arquivo));
                $midia = new Midia([
                    'title' => $newLostAndFound->title,
                    'url' => $url,
                    'file' => $arquivo,
                    'status' => 'ativo', // Status da mídia
                    'type' => 'imagem', // Tipo da mídia (por exemplo, imagem, PDF, etc.)
                    'user_id' => $request->input('user_id')
                ]);
                // Associar a mídia a uma entidade (por exemplo, Document)
                // Salvar o documento no banco de dados
                $newLostAndFound->midias()->save($midia);
            }
        }
        $midias = $newLostAndFound->midias;
        // Obtém a URL base para os ícones
        $iconBaseUrl = asset('assets/icons/');
        // Itere sobre cada item na coleção e adicione o tipo de arquivo e o ícone com URL completa
        foreach ($midias as $midia) {
            $fileExtension = strtolower(pathinfo($midia->url, PATHINFO_EXTENSION));
            if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
                $midia->type = 'imagem';
                $midia->icon = $midia->url;
            } elseif ($fileExtension === 'pdf') {
                $midia->type = 'pdf';
                $midia->icon = $iconBaseUrl . '/pdf.png';
            } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                $midia->type = 'word';
                $midia->icon = $iconBaseUrl . '/word.png';
            } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
                $midia->type = 'word';
                $midia->icon = $iconBaseUrl . '/excel.png';
            } else {
                $midia->type = 'outro';
                $midia->icon = $iconBaseUrl . '/outros.png';
            }
        }
        $newLostAndFound['midias'] = $midias;

        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $newLostAndFound,
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
                    if ($request->file('file')) {
                        $files = $request->file('file');
                        foreach ($files as  $key) {
                            $arquivo = $key->store('public/warnings/' . $lostAndFound->id);
                            $url = asset(Storage::url($arquivo));
                            $midia = new Midia([
                                'title' => $lostAndFound->title,
                                'url' => $url,
                                'file' => $arquivo,
                                'status' => 'ativo', // Status da mídia
                                'type' => 'imagem', // Tipo da mídia (por exemplo, imagem, PDF, etc.)
                                'user_id' => $request->input('user_id')
                            ]);
                            // Associar a mídia a uma entidade (por exemplo, Document)
                            // Salvar o documento no banco de dados
                            $lostAndFound->midias()->save($midia);
                        }
                    }
                    $midias = $lostAndFound->midias;
                    // Obtém a URL base para os ícones
                    $iconBaseUrl = asset('assets/icons/');
                    // Itere sobre cada item na coleção e adicione o tipo de arquivo e o ícone com URL completa
                    foreach ($midias as $midia) {
                        $fileExtension = strtolower(pathinfo($midia->url, PATHINFO_EXTENSION));
                        if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif'])) {
                            $midia->type = 'imagem';
                            $midia->icon = $midia->url;
                        } elseif ($fileExtension === 'pdf') {
                            $midia->type = 'pdf';
                            $midia->icon = $iconBaseUrl . '/pdf.png';
                        } elseif (in_array($fileExtension, ['doc', 'docx'])) {
                            $midia->type = 'word';
                            $midia->icon = $iconBaseUrl . '/word.png';
                        } elseif (in_array($fileExtension, ['xls', 'xlsx'])) {
                            $midia->type = 'word';
                            $midia->icon = $iconBaseUrl . '/excel.png';
                        } else {
                            $midia->type = 'outro';
                            $midia->icon = $iconBaseUrl . '/outros.png';
                        }
                    }
                    $lostAndFound['midias'] = $midias;
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
            'list' => $lostAndFound,
        ], 200);
    }
    /**
     * Inserir nova midia.
     *
     * @param int $id O ID do documento a ser excluído.
     *
     * @return \Illuminate\Http\JsonResponse 
     * */

     public function insertMidia($id, Request $request)
    {
        $lost = LostAndFound::find($id);

        $validator = Validator::make($request->all(), [
            'file' =>  'max:2M',
            'file.*' => 'mimes:jpg,png,jpeg',
            'user_id' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'code' => 400,
            ], 400);
        }
        // Se o aviso não for encontrado, retornar uma mensagem de erro
        if (!$lost) {
            return response()->json([
                'error' => 'Ocorrência inexistente',
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
            $arquivo = $key->store('public/lostAndFound/' . $id);
            $url = asset(Storage::url($arquivo));
            $midia = new Midia([
                'title' => $lost->title,
                'url' => $url,
                'file' => $arquivo,
                'status' => 'ativo', // Status da mídia
                'type' => 'imagem', // Tipo da mídia (por exemplo, imagem, PDF, etc.)
                'user_id' => $request->input('user_id')
            ]);
            // Associar a mídia a uma entidade (por exemplo, Document)
            // Salvar o documento no banco de dados
            try {
                $lost->midias()->save($midia);
            } catch (Exception $e) {
                // Tratar o erro
                return response()->json([
                    'error' => 'Erro ao salvar Imagem na galeria!',
                    'detail' => $e->getMessage(),
                    'code' => 500,
                ], 500);
            }

            // Retornar uma resposta de sucesso
            $lost->midias = $lost->midias;
            return response()->json([
                'error' => '',
                'success' => true,
                'list' => $lost,
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
        $lost = LostAndFound::find($id);

        $midias =  $lost->midias;
        foreach ($midias  as $midia) {
            $midia->delete();
            $midia = $midia->file;
            Storage::delete($midia);
        }
        // Se o aviso não for encontrado, retornar uma mensagem de erro
        if (!$lost) {
            return response()->json([
                'error' => 'Ocorrência inexistente',
                'code' => 404,
            ], 404);
        }

        // Tentar deletar o aviso
        try {
            $lost->delete();
           } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao deletar Ocorrência de achados e perdidos!',
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
