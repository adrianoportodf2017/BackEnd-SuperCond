<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profissional;
use Illuminate\Support\Facades\Validator;
use Exception;

class ProfissionaisController extends Controller
{
    public function getAll()
    {
        $profissionais = Profissional::all();

        if (!$profissionais) {
            return response()->json([
                'error' => 'Nenhum profissional encontrado',
                'code' => 404,
            ], 404);
        }

        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $profissionais,
        ], 200);
    }

    public function getById($id)
    {
        $profissional = Profissional::where('id', $id)->first();

        if (!$profissional) {
            return response()->json([
                'error' => "Profissional com ID {$id} não encontrado",
                'code' => 404,
            ], 404);
        }

        return response()->json([
            'error' => '',
            'list' => $profissional,
        ], 200);
    }

    public function getAllByUserId($id)
    {
        $profissionais = Profissional::where('user_id', $id)->get();

        if ($profissionais->isEmpty()) {
            return response()->json([
                'error' => 'Nenhum profissional encontrado para o usuário com ID ' . $id,
            ]);
        }

        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $profissionais,
        ], 200);
    }

    public function insert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2',
            'user_id' => 'required',
            'email' => 'required|email',
            'specialty' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }

        $newProfissional = new Profissional();
        $newProfissional->name = $request->input('name');
        $newProfissional->user_id = $request->input('user_id');
        $newProfissional->email = $request->input('email');
        $newProfissional->specialty = $request->input('specialty');
        $newProfissional->price = $request->input('price');
        $newProfissional->address = $request->input('address');
        $newProfissional->contact = $request->input('contact');
        $newProfissional->status = '0';

        try {
            $newProfissional->save();
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao salvar Profissional!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $newProfissional,
        ], 200);
    }

    public function update($id, Request $request)
    {
        $profissional = Profissional::find($id);

        if (!$profissional) {
            return response()->json([
                'error' => 'Profissional não encontrado',
                'code' => 404,
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2',
            'user_id' => 'required',
            'email' => 'required|email',
            'specialty' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }

        $profissional->name = $request->input('name');
        $profissional->user_id = $request->input('user_id');
        $profissional->email = $request->input('user_email');
        $profissional->specialty = $request->input('specialty');
        $profissional->price = $request->input('price');
        $profissional->address = $request->input('address');
        $profissional->contact = $request->input('contact');
        $profissional->status = $request->input('status');

        try {
            $profissional->save();
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao salvar Profissional!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $profissional,
        ], 200);
    }

    public function delete($id)
    {
        $profissional = Profissional::find($id);

        if (!$profissional) {
            return response()->json([
                'error' => 'Profissional inexistente',
                'code' => 404,
            ], 404);
        }

        try {
            $profissional->delete();
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao deletar Profissional!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        return response()->json([
            'error' => '',
            'success' => true,
        ], 200);
    }
}
