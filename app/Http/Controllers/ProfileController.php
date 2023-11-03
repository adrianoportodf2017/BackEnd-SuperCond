<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Profile;
use Illuminate\Support\Facades\Validator;
use Exception;


class ProfileController extends Controller
{
    public function getAll()
    {
        $profiles = Profile::all();
        if (!$profiles) {
            return response()->json([
                'error' => 'Nenhum Perfil Encontrado',
                'list' => ''
            ], 404);
        }
        return response()->json(['error' => '', 'list' => $profiles], 200);
    }

    public function getById($id)
    {
        $profile = Profile::find($id);
        if (!$profile) {
            return response()->json(['message' => 'Perfil não encontrado'], 404);
        }
        return response()->json(['profile' => $profile], 200);
    }

    public function update(Request $request, $id)
    {
        $profile = Profile::find($id);

        if (!$profile) {
            return response()->json(['message' => 'Perfil não encontrado'], 404);
        }

        // Valide os dados recebidos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'roles' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 400);
        }

        $profile->update($request->all());
        return response()->json([
            'error' => '',
            'profile' => $profile,
            'message' => 'Perfil atualizado com sucesso'
        ], 200);
    }

    public function insert(Request $request)
    {
        // Valide os dados recebidos
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'roles' => 'nullable|string',
            'status' => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $profile = Profile::create($request->all());

        return response()->json(['message' => 'Perfil criado com sucesso', 'profile' => $profile], 201);
    }


    public function updateStatus($id, Request $request)
    {
        $array = ['error' => ''];
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        } else {
            $item = Profile::find($id);
            $item->status = $request->input('status');
             // Salvar o documento no banco de dados
             try {
                $item->save();
            } catch (Exception $e) {
                // Tratar o erro
                return response()->json([
                    'error' => 'Erro ao salvar Aviso!',
                    'detail' => $e->getMessage(),
                    'code' => 500,
                ], 500);
            }
    
            // Retornar uma resposta de sucesso com os dados da notícia
            return response()->json([
                'error' => '',
                'success' => true,
                'list' => $item,
            ], 201);
        }

    }

    public function delete($id)
    {
        $profile = Profile::find($id);
        if (!$profile) {
            return response()->json([
                'error' => 'Perfil não encontrado'
            ], 404);
        }

        $profile->delete();
        return response()->json([
            'error' => '',
            'message' => 'Perfil excluído com sucesso'
        ], 200);
    }
}
