<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Storage;
use App\Models\Midia;
use Illuminate\Support\Facades\Validator;
use Exception;



class FolderController extends Controller
{
    public function getAll()
    {
        $folders = $this->getFolderWithChildren(null); // Chame a função com null para obter todas as pastas

        if (!$folders) {
            // Se não houver pastas, retorne um erro 404
            return response()->json([
                'error' => 'Nenhuma pasta encontrada',
                'code' => 404,
            ], 404);
        }

        return response()->json($folders, 200); // Adicione o código de status HTTP 200
    }

    public function getAllPublic()
    {
        $folders = $this->getFolderWithChildren(null, '1'); // Chame a função com null para obter todas as pastas

        if (!$folders) {
            // Se não houver pastas, retorne um erro 404
            return response()->json([
                'error' => 'Nenhuma pasta encontrada',
                'code' => 404,
            ], 404);
        }

        return response()->json($folders, 200); // Adicione o código de status HTTP 200
    }



    public function getById($id)
    {
        $folder = Folder::find($id);

        if (!$folder) {
            // Se a pasta não for encontrada, retorne um erro 404
            return response()->json([
                'error' => 'Pasta não encontrada',
                'code' => 404,
            ], 404);
        }



        // Recupere a coleção de mídias
        $midias = $folder->midias;

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

        if (empty($folder->thumb)) {
            $folder->thumb =  $iconBaseUrl . '/folder.png';
        }

        $folder['children'] = $this->getFolderWithChildren($id);
        $folder['midias'] = $midias;

        return response()->json($folder, 200);
    }


    public function insert(Request $request)
    {
        // Validar os dados da requisição
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            'file.*' => 'max:10000|mimes:jpg,png,jpeg,doc,docx,pdf,xls,xlsx',
            'thumb' => 'mimes:jpg,png,jpeg',
        ]);

        // Retornar uma mensagem de erro se a validação falhar
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }

        // Verificar se o arquivo é válido
        if ($request->file('thumb') && !$request->file('thumb')->isValid()) {
            return response()->json([
                'error' => 'O arquivo enviado não é válido',
                'code' => 400,
            ], 400);
        }

        $arquivo = '';
        $url = '';
        if ($request->hasfile('thumb')) {
            // Salvar o arquivo no armazenamento
            $arquivo = $request->file('thumb')->store('public/folders/thumb');
            $url = asset(Storage::url($arquivo));
        }

        // Criar uma nova instância de Folder
        $newFolder = new Folder();
        $newFolder->title = $request->input('title');
        $newFolder->content = $request->input('content');
        $newFolder->thumb = $url;
        $newFolder->thumb_file = $arquivo;
        $newFolder->status = $request->input('status');
        $newFolder->parent_id = $request->input('parent_id');

        // Verificar a nova ordem
        $newOrder = $request->input('order');
        if (!empty($newOrder)) {
            // Atualizar a ordem das outras pastas, se necessário
            Folder::where('order', '>=', $newOrder)->increment('order');
            $newFolder->order = $newOrder;
        }

        // Salvar o documento no banco de dados
        try {
            $newFolder->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Pasta!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        // Salvar os arquivos associados à nova pasta, se houver
        if ($request->file('file')) {
            $files = $request->file('file');
            foreach ($files as $key) {
                $arquivo = $key->store('public/folders/' . $newFolder->id);
                $url = asset(Storage::url($arquivo));
                $midia = new Midia([
                    'title' => $newFolder->title,
                    'url' => $url,
                    'file' => $arquivo,
                    'status' => 'ativo', // Status da mídia
                    'type' => '', // Tipo da mídia (por exemplo, imagem, PDF, etc.)
                    //'user_id' => $request->input('user_id')
                ]);
                // Associar a mídia à pasta
                $newFolder->midias()->save($midia);
            }
        }

        // Retornar uma resposta de sucesso
        $newFolder->midias = $newFolder->midias;
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $newFolder,
        ], 201);
    }



    public function update($id, Request $request)
    {
        $array['id'] =  $id;
        // Buscar o documento pelo ID
        $folder = Folder::find($id);
        $arquivo = $folder->thumb_file;
        $url =  $folder->thumb;

        // Se a pasta não for encontrada, retornar uma mensagem de erro
        if (!$folder) {
            return response()->json([
                'error' => 'Pasta inexistente',
                'code' => 404,
            ], 404);
        }

        // Validar os dados da requisição
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            'file.*' => 'max:10000|mimes:jpg,png,jpeg,doc,docx,pdf,xls,xlsx',
            'thumb' => 'mimes:jpg,png,jpeg',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'code' => 400,
            ], 400);
        }

        if ($request->file('thumb')) {
            // Salvar o arquivo no armazenamento
            $arquivo = $request->file('thumb')->store('public/folders/thumb');
            $url = asset(Storage::url($arquivo));
            $thumb_delete = $folder->thumb_file;
            Storage::delete($thumb_delete);
        }

        // Verificar a nova ordem
        $newOrder = $request->input('order');

        // Atualizar a ordem das outras pastas, se necessário
        if (!empty($newOrder) && $newOrder != $folder->order) {
            Folder::where('order', '>=', $newOrder)->increment('order');
        }

        $folder->title = $request->input('title');
        $folder->content = $request->input('content');
        $folder->thumb_file = $arquivo;
        $folder->thumb = $url;
        $folder->status = $request->input('status');
        if (!empty($newOrder)) {
            $folder->order = $newOrder;
        }

        // Salvar o documento no banco de dados
        try {
            $folder->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar a Pasta!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $folder,
        ], 200);
    }


    public function delete($id)
    {
        try {
            // Verifica se um ID foi fornecido
            if ($id === null) {
                return; // Não foi fornecido um ID válido
            }

            // Busca as pastas com base no ID fornecido
            $folder = Folder::find($id);

            if (!$folder) {
                return; // Pasta não encontrada
            }

            // Recursivamente, exclua todas as pastas filhas
            $children = Folder::where('parent_id', $id)->get();

            foreach ($children as $child) {
                $this->delete($child->id);
            }

            // Agora que todas as pastas filhas foram excluídas, você pode excluir esta pasta
            $midias =  $folder->midias;
            foreach ($midias  as $midia) {
                $midia->delete();
                $midia = $midia->file;
                Storage::delete($midia);
            }
            $folder->delete();
        } catch (Exception $e) {
            // Trate a exceção aqui, como registrar o erro ou enviar uma resposta de erro para o usuário.
            // Você pode usar $e->getMessage() para obter informações sobre o erro.
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao deletar Pasta!',
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


    public function insertMidia($id, Request $request)
    {
        $folder = Folder::find($id);

        $validator = Validator::make($request->all(), [
            'file.*' => 'required|max:200000|mimes:jpg,png,jpeg,doc,docx,pdf,xls,xlsx',
            // 'file' => 'max:10000|mimes:jpg,png,jpeg,doc,docx,pdf,xls,xlsx',
            //'user_id' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'code' => 400,
            ], 400);
        }
        // Se o aviso não for encontrado, retornar uma mensagem de erro
        if (!$folder) {
            return response()->json([
                'error' => 'Pasta inexistente',
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

        //  var_dump($files);

        foreach ($files as $file) {
            if (!$file->isValid()) {
                return response()->json([
                    'error' => 'O arquivo enviado não é válido',
                    'code' => 400,
                ], 400);
            }

            $title = $file->getClientOriginalName(); // Use o nome original do arquivo como título
            $extension = $file->getClientOriginalExtension();


            $count = 1;
            $newTitle = $title;
            $newTitle = preg_replace('/[^a-zA-Z0-9.-]+/', '',   $newTitle);

            while (Midia::where('slug', $newTitle)->count() > 0) {
                // Se uma mídia com o mesmo título existir, adicione um número ao título para torná-lo único
                $newTitle = pathinfo($title, PATHINFO_FILENAME) . '-' . $count . '.' . $extension;
                $newTitle = preg_replace('/[^a-zA-Z0-9.-]+/', '',   $newTitle);
                $count++;
            }

            $arquivo = $file->storeAs('public/folders/' . $id, $newTitle); // Salve o arquivo com o título ajustado
            $url = asset(Storage::url($arquivo));

            $midia = new Midia([
                'title' => $request->input('title') ? $request->input('title') : pathinfo($title, PATHINFO_FILENAME),
                'slug' => $newTitle,
                'url' => $url,
                'file' => $arquivo,
                'status' => 'ativo',
                'type' => $extension, // Defina o tipo com base na extensão do arquivo
                'user_id' => $request->input('user_id')
            ]);
            // Associar a mídia a uma entidade (por exemplo, Document)
            // Salvar o documento no banco de dados
            try {
                $folder->midias()->save($midia);
            } catch (Exception $e) {
                // Tratar o erro
                return response()->json([
                    'error' => 'Erro ao salvar Imagem na galeria!',
                    'detail' => $e->getMessage(),
                    'code' => 500,
                ], 500);
            }
        }
        // Retornar uma resposta de sucesso
        $folder->midias = $folder->midias;
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $folder,
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
     * Recupera as pastas filhas de uma pasta, incluindo os campos 'id', 'title' e 'thumb'.
     *
     * @param int $id O ID da pasta para buscar suas pastas filhas.
     * @return \Illuminate\Database\Eloquent\Collection|null Coleção de pastas filhas com campos específicos ou null se a pasta não for encontrada.
     */
    private function getFolderWithChildren($id = null, $public = null)
    {
        // Verifica se um ID foi fornecido
        if ($id == null) {
            // Busca todas as pastas raiz (sem pai)::where('status', 1)->orderBy('created_at', 'desc')->get();
           if($public == null){
            $folders = Folder::whereNull('parent_id')->orderBy('title')->get();   
           }else{
            $folders = Folder::where('status', '1')->whereNull('parent_id')->orderBy('title')->get(); 
           }          
        // Mapeia cada pasta para incluir seus filhos
        $folders = $folders->map(function ($folder) use ($public) {
            $folder->children = $this->getFolderWithChildren($folder->id, $public);
                // Verifica se a propriedade "thumb" é null e cria o objeto "icon" com um valor padrão se for null
                $folder->icon =  asset('assets/icons/folder.png');

                return $folder;
            });
            $folders = $this->updateFolderOrder($folders); /*Adicionar order automaticamente*/
            return $folders;
        }

        // Se um ID foi fornecido, busca as pastas com base nesse ID
        $folder = Folder::find($id);

        if (!$folder) {
            return null;
        }

        $children = Folder::select('id', 'order', 'title', 'thumb', 'status', 'created_at', 'updated_at')
            ->where('parent_id', $id)
            ->orderBy('title')
            ->get();

            $children = $children->map(function ($child)  use ($public) {
            $child->children = $this->getFolderWithChildren($child->id, $public);

            // Verifica se a propriedade "thumb" é null e cria o objeto "icon" com um valor padrão se for null
            $child->icon = asset('assets/icons/folder.png');

            return $child;
        });
        $children = $this->updateFolderOrder($children); /*Adicionar order automaticamente*/
        return $children;
    }

    private function updateFolderOrder($folders)
    {
        $cont = 0;
        $foldersToUpdate = [];

        foreach ($folders as $folder) {
            if ($folder->order == null || $folder->order == '') {
                $folder->order = $cont;
            }
            $cont++;
            $foldersToUpdate[] = $folder;
        }

        // Ordenar as pastas pela coluna 'order'
        usort($foldersToUpdate, function ($a, $b) {
            return $a->order <=> $b->order;
        });

        return $foldersToUpdate;
    }
}
