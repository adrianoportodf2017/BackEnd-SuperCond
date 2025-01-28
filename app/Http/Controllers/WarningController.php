<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Warning;
use App\Models\Midia;
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
                'error' => 'Nenhuma Galeria Encontrado',
                'code' => 404,
            ], 404);
        }
        // Retornar uma resposta de sucesso com a lista de ocorrencias
        $result = [];

        $result = [];
        foreach ($warnings as $warning) {
            // Recupere a coleção de mídias
            $midias = $warning->midias;
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
            $warning['midias'] = $midias;
            $result[] = $warning;
        }

        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $result,
        ], 200);
    }


    public function getAllByUserId($id)

    {
        $warnings =  Warning::where('owner_id', $id)->get();

        // Retornar uma mensagem de erro se não houver ocorrencias
        if (!$warnings) {
            return response()->json([
                'error' => 'Nenhuma Ocorrência Encontrado',
                'code' => 404,
            ], 404);
        }
        // Retornar uma resposta de sucesso com a lista de ocorrencias
        $result = [];
        foreach ($warnings as $warning) {
            // Recupere a coleção de mídias
            $midias = $warning->midias;
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
            $warning['midias'] = $midias;
            $result[] = $warning;
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
                'error' => "Ocorrência não encontrado",
                'code' => 404,
            ], 404);
        }
        $warning->midias = $warning->midias;
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
            'category' => 'required|min:2',
            //'file' => 'required',
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
        if ($request->hasfile('file')) {
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
        }

        // Criar um novo documento
        $newWarning = new Warning();
        $newWarning->title = $request->input('title');
        $newWarning->content = $request->input('content');
        $newWarning->category = $request->input('category');
        $newWarning->notes = $request->input('notes');
        $newWarning->owner_id = $request->input('owner_id');
        $newWarning->unit_id = $request->input('unit_id');
        $newWarning->status = '1';


        // Salvar o documento no banco de dados
        try {
            $newWarning->save();
            $mailController = new MailController();
            $emailRequest = new Request([
                'to' => 'sitesprontobr@gmail.com',
                'subject' => 'Nova Ocorrência: ' . $newWarning->title,
                'content' => "Uma nova ocorrência foi registrada:\n\n" .
                            "Título: {$newWarning->title}\n" .
                            "Categoria: {$newWarning->category}\n" .
                            "Conteúdo: {$newWarning->content}\n" .
                            "Notas: {$newWarning->notes}",
            ]);
            
            $mailController->enviarEmail($emailRequest);
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
                $arquivo = $key->store('public/warnings/' . $newWarning->id);
                $url = asset(Storage::url($arquivo));
                $midia = new Midia([
                    'title' => $newWarning->title,
                    'url' => $url,
                    'file' => $arquivo,
                    'status' => 'ativo', // Status da mídia
                    'type' => 'imagem', // Tipo da mídia (por exemplo, imagem, PDF, etc.)
                    'user_id' => $request->input('user_id')
                ]);
                // Associar a mídia a uma entidade (por exemplo, Document)
                // Salvar o documento no banco de dados
                $newWarning->midias()->save($midia);
            }
        }
        $midias = $newWarning->midias;
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
        $newWarning['midias'] = $midias;

        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $newWarning,
        ], 201);
    }

    public function update($id, Request $request)
    {
        $array['id'] =  $id;
        // Buscar o documento pelo ID
        $warning = Warning::find($id);
        // Validar os dados da requisição
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            'file.*' => 'mimes:jpg,png,pdf,jpeg',
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
                $warning->category = $request->input('category');
                $warning->status = $request->input('status');
                // Salvar o documento no banco de dados
                try {
                    $warning->save();
                    // Retornar uma resposta de sucesso
                    if ($request->file('file')) {
                        $files = $request->file('file');
                        foreach ($files as  $key) {
                            $arquivo = $key->store('public/warnings/' . $warning->id);
                            $url = asset(Storage::url($arquivo));
                            $midia = new Midia([
                                'title' => $warning->title,
                                'url' => $url,
                                'file' => $arquivo,
                                'status' => 'ativo', // Status da mídia
                                'type' => 'imagem', // Tipo da mídia (por exemplo, imagem, PDF, etc.)
                                'user_id' => $request->input('user_id')
                            ]);
                            // Associar a mídia a uma entidade (por exemplo, Document)
                            // Salvar o documento no banco de dados
                            $warning->midias()->save($midia);
                        }
                    }
                    $midias = $warning->midias;
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
                    $warning['midias'] = $midias;
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
            'list' => $warning,
        ], 200);
    }
    /**
     * Exclui um documento.
     *
     * @param int $id O ID do documento a ser excluído.
     *
     * @return \Illuminate\Http\JsonResponse 
     * */

    public function insertMidia($id, Request $request)
    {
        $warning = Warning::find($id);

        $validator = Validator::make($request->all(), [
            'file' =>  'max:10M',
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
        if (!$warning) {
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
            $arquivo = $key->store('public/warnings/' . $id);
            $url = asset(Storage::url($arquivo));
            $midia = new Midia([
                'title' => $warning->title,
                'url' => $url,
                'file' => $arquivo,
                'status' => 'ativo', // Status da mídia
                'type' => 'imagem', // Tipo da mídia (por exemplo, imagem, PDF, etc.)
                'user_id' => $request->input('user_id')
            ]);
            // Associar a mídia a uma entidade (por exemplo, Document)
            // Salvar o documento no banco de dados
            try {
                $warning->midias()->save($midia);
            } catch (Exception $e) {
                // Tratar o erro
                return response()->json([
                    'error' => 'Erro ao salvar Imagem na galeria!',
                    'detail' => $e->getMessage(),
                    'code' => 500,
                ], 500);
            }

            // Retornar uma resposta de sucesso
            $warning->midias = $warning->midias;
            return response()->json([
                'error' => '',
                'success' => true,
                'list' => $warning,
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
        $warning = Warning::find($id);

        $midias =  $warning->midias;
        foreach ($midias  as $midia) {
            $midia->delete();
            $midia = $midia->file;
            Storage::delete($midia);
        }
        // Se o aviso não for encontrado, retornar uma mensagem de erro
        if (!$warning) {
            return response()->json([
                'error' => 'Galeria inexistente',
                'code' => 404,
            ], 404);
        }

        // Tentar deletar o aviso
        try {
            $warning->delete();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao deletar Ocorrência!',
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
