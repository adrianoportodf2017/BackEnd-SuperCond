<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Gallery;
use App\Models\Midia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Exception;


class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function getAll()
    {
        $gallerys =  Gallery::all();

        // Retornar uma mensagem de erro se não houver ocorrencias
        if (!$gallerys) {
            return response()->json([
                'error' => 'Nenhuma Galeria Encontrado',
                'code' => 404,
            ], 404);
        }
        // Retornar uma resposta de sucesso com a lista de ocorrencias
        // Retornar uma resposta de sucesso
        // Retornar uma resposta de sucesso
        $cont = '0';
        foreach ($gallerys as $gallery) {
            $midias = $gallery->midias;
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
            $gallery['midias'] = $midias;
            $data[$cont] = $gallery;
            $cont++;
        }
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $data,
        ], 200);
    }

    
    public function getAllPublic()
    {
        $gallerys = Gallery::where('status', '1')->get();
 // Retornar uma resposta de sucesso com a lista de ocorrencias
        // Retornar uma resposta de sucesso
        // Retornar uma resposta de sucesso
        $cont = '0';
        $data = [];
        foreach ($gallerys as $gallery) {
            $midias = $gallery->midias;         
            $gallery['midias'] = $midias;
            $data[$cont] = $gallery;
            $cont++;
        }
        // Retornar uma mensagem de erro se não houver ocorrencias
        if (!$gallerys) {
            return response()->json([
                'error' => 'Nenhuma Galeria Encontrado',
                'code' => 404,
            ], 404);
        }
        // Retornar uma resposta de sucesso com a lista de ocorrencias
        // Retornar uma resposta de sucesso
      
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $data,
        ], 200);
    }

    /**
     * Obtém um galeria pelo ID.
     *
     * @param int $id O ID do galeria a ser obtido.
     *
     * @return \App\Models\Doc
     */
    public function getById($id)
    {
        $gallery = Gallery::where('id', $id)->first();

        if (!$gallery) {
            return response()->json([
                'error' => "Galeria não encontrado",
                'code' => 404,
            ], 404);
        }
        // Retornar uma resposta de sucesso
        // Retornar uma resposta de sucesso
        $midias = $gallery->midias;
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
        $gallery['midias'] = $midias;
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $gallery,
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

        $newGallery = new Gallery();


        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            'file' =>  'max:10M',
            'file.*' => 'mimes:jpg,png,jpeg',
            'thumb' => 'mimes:jpg,png,jpeg',

        ]);

        // Retornar uma mensagem de erro se a validação falhar
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }
        // Criar um novo documento
        // Verificar se o arquivo é válido
        if ($request->file('thumb') && !$request->file('thumb')->isValid()) {
            return response()->json([
                'error' => 'O arquivo enviado não é válido',
                'code' => 400,
            ], 400);
        }
        if ($request->hasfile('thumb')) {
            // Salvar o arquivo no armazenamento
            $arquivo = $request->file('thumb')->store('public/gallery/thumb');
            $url = asset(Storage::url($arquivo));
        } else {
            $arquivo = '';
            $url = '';
        }

        $newGallery->title = $request->input('title');
        $newGallery->content = $request->input('content');
        $newGallery->thumb = $url;
        $newGallery->thumb_file = $arquivo;
        $newGallery->status = $request->input('status');
        $newGallery->tags = $request->input('tags');

        // Salvar o documento no banco de dados
        try {
            $newGallery->save();
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
                $arquivo = $key->store('public/gallery/' . $newGallery->id);
                $url = asset(Storage::url($arquivo));
                $midia = new Midia([
                    'title' => $newGallery->title,
                    'url' => $url,
                    'file' => $arquivo,
                    'status' => 'ativo', // Status da mídia
                    'type' => 'imagem', // Tipo da mídia (por exemplo, imagem, PDF, etc.)
                    'user_id' => $request->input('user_id')
                ]);
                // Associar a mídia a uma entidade (por exemplo, Document)
                // Salvar o documento no banco de dados
                $newGallery->midias()->save($midia);
            }
        }
        // Retornar uma resposta de sucesso
        $midias = $newGallery->midias;
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
        $newGallery['midias'] = $midias;
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $newGallery,
        ], 201);
    }



    public function update($id, Request $request)
    {
        $array['id'] =  $id;
        // Buscar o documento pelo ID
        $gallery = Gallery::find($id);
        $arquivo = $gallery->thumb_file;
        $url =  $gallery->thumb;



        // Se o aviso não for encontrado, retornar uma mensagem de erro
        if (!$gallery) {
            return response()->json([
                'error' => 'Galeria inexistente',
                'code' => 404,
            ], 404);
        }

        // Validar os dados da requisição
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'code' => 400,
            ], 400);
        }

        if ($request->file('thumb')) {
            // Validar os dados da requisição
            $validator = Validator::make($request->all(), [
                'thumb' => 'mimes:jpg,png,jpeg',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'O arquivo enviado não é válido',
                    'code' => 400,
                ], 400);
            }

            // Salvar o arquivo no armazenamento
            $arquivo = $request->file('thumb')->store('public/gallery/thumb');
            $url = asset(Storage::url($arquivo));
            $thumb_delete = $gallery->thumb_file;
            Storage::delete($thumb_delete);
        }


        $gallery->title = $request->input('title');
        $gallery->content = $request->input('content');
        $gallery->thumb_file = $arquivo;
        $gallery->thumb = $url;
        $gallery->status = $request->input('status');
        // Salvar o documento no banco de dados
        try {
            $gallery->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Novo item!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        // Retornar uma resposta de sucesso
        // Retornar uma resposta de sucesso
        $midias = $gallery->midias;
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
        $gallery['midias'] = $midias;
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $gallery,
        ], 200);
    }

    public function insertMidia($id, Request $request)
    {
        $gallery = Gallery::find($id);

        $validator = Validator::make($request->all(), [
            'file.*' => 'required|max:200000|mimes:jpg,png,jpeg,doc,docx,pdf,xls,xlsx',

            // 'user_id' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'messages' => $validator->errors()->all(),
                'code' => 400,
            ], 400);
        }

        // Se o aviso não for encontrado, retornar uma mensagem de erro
        if (!$gallery) {
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

            $arquivo = $key->store('public/gallery/' .   $id);
            $url = asset(Storage::url($arquivo));
            $midia = new Midia([
                'title' => $gallery->title,
                'url' => $url,
                'file' => $arquivo,
                'status' => 'ativo', // Status da mídia
                'type' => 'imagem', // Tipo da mídia (por exemplo, imagem, PDF, etc.)
                'user_id' =>   $gallery->user_id
            ]);
            // Associar a mídia a uma entidade (por exemplo, Document)
            // Salvar o documento no banco de dados
            try {
                $gallery->midias()->save($midia);
            } catch (Exception $e) {
                // Tratar o erro
                return response()->json([
                    'error' => 'Erro ao salvar Novo item!',
                    'detail' => $e->getMessage(),
                    'code' => 500,
                ], 500);
            }
        }
        // Retornar uma resposta de sucesso
        $midias = $gallery->midias;
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
        $gallery['midias'] = $midias;
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $gallery,
        ], 200);
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
        $gallery = Gallery::find($id);

        $midias =  $gallery->midias;
        foreach ($midias  as $midia) {
            $midia->delete();
            $midia = $midia->file;
            Storage::delete($midia);
        }
        // Se o aviso não for encontrado, retornar uma mensagem de erro
        if (!$gallery) {
            return response()->json([
                'error' => 'Galeria inexistente',
                'code' => 404,
            ], 404);
        }

        // Tentar deletar o aviso
        try {
            $gallery->delete();
            $fileDelete = $gallery->thumb_file;
            Storage::delete($fileDelete);
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao deletar Galeria!',
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
