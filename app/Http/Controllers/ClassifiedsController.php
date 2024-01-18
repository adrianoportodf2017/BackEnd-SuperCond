<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Classified;
use App\Models\User;

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
        $classifieds = Classified::leftJoin('users', 'classifieds.user_id', '=', 'users.id')
        ->select('classifieds.*', 'users.name', 'users.email')
        ->get();

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
            // Recupere a coleção de mídias
            $midias = $classified->midias;
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
            $classified['midias'] = $midias;
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
        $classified = Classified::where('id', $id)->first();
        $classified->midias  = $classified->midias;


        if (!$classified) {
            return response()->json([
                'error' => "Item com ID {$id} não encontrado",
                'code' => 404,
            ], 404);
        }
        return response()->json([
            'error' => '',
            'list' => $classified,
            // Outros dados de resultado aqui...
        ], 200);
    }

    public function getAllByUserId($id)
    {
        $classifieds = Classified::where('user_id', $id)
            ->leftJoin('users', 'classifieds.user_id', '=', 'users.id')
            ->select('classifieds.*', 'users.name', 'users.email')
            ->get();
    
        // Retornar uma mensagem de erro se não houver ocorrências
        if ($classifieds->isEmpty()) {
            return response()->json([
                'error' => '',
                'list' => '',
                'code' => 404,
            ], 404);
        }
    
        // Retornar uma resposta de sucesso com a lista de ocorrências
        $result = [];
        foreach ($classifieds as $classified) {
            // Recupere a coleção de mídias
            $midias = $classified->midias;
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
                    $midia->type = 'excel';
                    $midia->icon = $iconBaseUrl . '/excel.png';
                } else {
                    $midia->type = 'outro';
                    $midia->icon = $iconBaseUrl . '/outros.png';
                }
            }
            $classified['midias'] = $midias;
            $result[] = $classified;
        }
    
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $result,
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

        $newclassifieds = new Classified();


        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            'file.*' => 'max:10000|mimes:jpg,png,jpeg',
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

        if ($request->hasfile('thumb')) {

            $validator = Validator::make($request->all(), [
                'thumb' => 'required|mimes:jpg,png,jpeg'
            ]);

            if ($request->file('thumb')->isValid()) {
                $arquivo = $request->file('thumb')->store('public/classifieds/thumb');
                $url = asset(Storage::url($arquivo));
            } else {
                $array['error'] = $validator->errors()->first();
            }
        } else {
            $url  = '';
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
        $newclassifieds->title = $request->input('title');
        $newclassifieds->user_id = $request->input('user_id');
        $newclassifieds->content = $request->input('content');
        $newclassifieds->thumb = $url ;
        $newclassifieds->price = $request->input('price');
        $newclassifieds->address = $request->input('address');
        $newclassifieds->contact = $request->input('contact');
        $newclassifieds->category_id = $request->input('category_id');
        $newclassifieds->status = '0';


        // Salvar o documento no banco de dados
        try {
            $newclassifieds->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Classificados!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        // Retornar uma resposta de sucesso
        if ($request->hasfile('file')) {
            foreach ($files as  $key) {
                $arquivo = $key->store('public/classifieds/' . $newclassifieds->id);
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
                    $newclassifieds->midias()->save($midia);
                } catch (Exception $e) {
                    // Tratar o erro
                    return response()->json([
                        'error' => 'Erro ao salvar Novo arquivo!',
                        'detail' => $e->getMessage(),
                        'code' => 500,
                    ], 500);
                }
            }
        }
        // Retornar uma resposta de sucesso
        $newclassifieds->midias  = $newclassifieds->midias;

        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $newclassifieds,
        ], 200);
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

                if ($request->hasfile('thumb')) {

                    $validator = Validator::make($request->all(), [
                        'thumb' => 'required|mimes:jpg,png,jpeg'
                    ]);
        
                    if ($request->file('thumb')->isValid()) {
                        $arquivo = $request->file('thumb')->store('public/classifieds/thumb');
                        $url = asset(Storage::url($arquivo));
                        $thumbDelete = $classified->thumb;
                        // Converta a URL em um caminho relativo ao sistema de arquivos
                        $relativePath = str_replace(asset(''), '', $thumbDelete);
                        $relativePath = str_replace('storage', '', $relativePath);
                        Storage::delete('public' . $relativePath);
                        $classified->thumb =  $url;
        
                    } else {
                        $array['error'] = $validator->errors()->first();
                    }
                }


                $classified->title = $request->input('title');
                $classified->content = $request->input('content');
                $classified->price = $request->input('price');
                $classified->address = $request->input('address');
                $classified->contact = $request->input('contact');
                $classified->category_id = $request->input('category_id');
                $classified->status = $request->input('status');
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
            $arquivo = $key->store('public/classifieds/' .   $classified->user_id);
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
                $midias = $classified->midias;
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
                $classified['midias'] = $midias;
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
            // Agora que todas as pastas filhas foram excluídas, você pode excluir esta pasta
            $midias =  $classified->midias;
            foreach ($midias  as $midia) {
                $midia->delete();
                $midia = $midia->file;
                Storage::delete($midia);
            }
            $classified->delete();
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
