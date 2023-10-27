<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
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

    public function getById($id)
    {

        $folder = Folder::find($id);
        $folderChidrens = $this->getFolderWithChildren($id);

        if (!$folder) {
            // Se a pasta não for encontrada, retorne um erro 404
            return response()->json([
                'error' => 'Pasta não encontrada',
                'code' => 404,
            ], 404);
        }

        return response()->json($folder, 200); // Adicione o código de status HTTP 200
    }


    public function insert(Request $request)
    {      
        // Validar os dados da requisição

        $newFolder = new Folder();
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
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
            $arquivo = $request->file('thumb')->store('public/folders/thumb');
            $url = asset(Storage::url($arquivo));
        } else {
            $arquivo = '';
            $url = '';
        }
        $newFolder->title = $request->input('title');
        $newFolder->content = $request->input('content');
        $newFolder->thumb = $url;
        $newFolder->thumb_file = $arquivo;
        $newFolder->status = $request->input('status');
        $newFolder->parent_id = $request->input('parent_id');

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
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $newFolder,
        ], 201);
    }


    public function update(Request $request, $id)
    {
        $folder = Folder::find($id);
        $folder->update($request->all());
        return response()->json($folder);
    }

    public function destroy($id)
    {
        Folder::destroy($id);
        return response()->json(['message' => 'Folder deleted']);
    }



    /**
     * Recupera as pastas filhas de uma pasta, incluindo os campos 'id', 'title' e 'thumb'.
     *
     * @param int $id O ID da pasta para buscar suas pastas filhas.
     * @return \Illuminate\Database\Eloquent\Collection|null Coleção de pastas filhas com campos específicos ou null se a pasta não for encontrada.
     */
    private function getFolderWithChildren($id = null)
    {
        // Verifica se um ID foi fornecido
        if ($id === null) {
            // Busca todas as pastas raiz (sem pai)
            $folders = Folder::whereNull('parent_id')->get();

            $folders = $folders->map(function ($folder) {
                $folder->children = $this->getFolderWithChildren($folder->id);
                return $folder;
            });

            return $folders;
        }

        // Se um ID foi fornecido, busca as pastas com base nesse ID
        $folder = Folder::find($id);

        if (!$folder) {
            return null;
        }

        $children = Folder::select('id', 'title', 'thumb')
            ->where('parent_id', $id)
            ->get();

        $children = $children->map(function ($child) {
            $child->children = $this->getFolderWithChildren($child->id);
            return $child;
        });

        return $children;
    }
}
