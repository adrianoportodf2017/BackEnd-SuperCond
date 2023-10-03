<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Billet;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Exception;



class BilletController extends Controller
{

    /**
     * Obtém todos os boletos.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAll()
    {
        $billets = Billet::all();

        // Retornar uma mensagem de erro se não houver boletos
        if (!$billets) {
            return response()->json([
                'error' => 'Nenhum aviso encontrado',
                'code' => 404,
            ], 404);
        }
        // Retornar uma resposta de sucesso com a lista de boletos
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $billets,
        ], 200);
    }
    /**
     * Obtém um boleto pelo ID.
     *
     * @param int $id O ID do boleto a ser obtido.
     *
     * @return \App\Models\Billet
     */
    public function getById($id)
    {
        $billet = Billet::where('id', $id)->first();

        if (!$billet) {
            return response()->json([
                'error' => "Boleto com ID {$id} não encontrado",
                'code' => 404,
            ], 404);
        }

        return response()->json([
            'error' => '',
            'list' => $billet,
            // Outros dados de resultado aqui...
        ], 200);
    }

    /**
     * Insere um novo boleto.
     *
     * @param \Illuminate\Http\Request $request Os dados do boleto a ser inserido.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function insert(Request $request)
    {

        // title, content, price, date_vue, file, unit_id, owner_id, date_payment, status

        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            'file' => 'required|mimes:jpg,png,pdf,jpeg|max:500',
            'price' => 'required',
            'date_vue' => 'required',
            'required_one_of' => [
                'unit_id',
                'owner_id',
            ],
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
        if (!$request->file('file')->isValid()) {
            return response()->json([
                'error' => 'O arquivo enviado não é válido',
                'code' => 400,
            ], 400);
        }

        // Salvar o arquivo no armazenamento
        $arquivo = $request->file('file')->store('public/billets');
        $url = asset(Storage::url($arquivo));

        // Criar um novo boleto
        // title, content, price, date_vue, file, unit_id, owner_id, date_payment, status

        $newBillet = new Billet();
        $newBillet->title = $request->input('title');
        $newBillet->content = $request->input('content');
        $newBillet->price = $request->input('price');
        $newBillet->date_vue = $request->input('date_vue');
        $newBillet->unit_id = $request->input('unit_id');
        $newBillet->owner_id = $request->input('owner_id');
        $newBillet->date_payment = $request->input('date_payment');
        $newBillet->status = $request->input('status');
        $newBillet->fileurl = $url;
        $newBillet->filename = $arquivo;


        // Salvar o boleto no banco de dados
        try {
            $newBillet->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Boleto!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        // Retornar uma resposta de sucesso
        return response()->json([
            'error' => '',
            'success' => true,
            'billetument' => $newBillet,
        ], 201);
    }
    public function update($id, Request $request)
    {
        $array['id'] =  $id;
        // Buscar o boleto pelo ID
        $billet = Billet::find($id);

        // return var_dump($_POST);die;

        // Retornar uma mensagem de erro se o boleto não for encontrado
        if (!$billet) {
            return response()->json([
                'error' => 'Boleto não encontrado',
                'code' => 404,
            ], 404);
        }
        if ($request->hasfile('file')) {
            $validator = Validator::make($request->all(), [
                'title' => 'required|min:2',
                'file' => 'required|mimes:jpg,png,pdf,jpeg|max:500',
                'price' => 'required',
                'date_vue' => 'required',
                'required_one_of' => [
                    'unit_id',
                    'owner_id',
                ],
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()->first(),
                    'code' => 400,
                ], 400);
            } else {

                $arquivo = $request->file('file')->store('public/billets');
                $url = asset(Storage::url($arquivo));
                if ($billet) {
                    $fileDelete = $billet->filename;
                    $billet->title = $request->input('title');
                    $billet->content = $request->input('content');
                    $billet->price = $request->input('price');
                    $billet->date_vue = $request->input('date_vue');
                    $billet->unit_id = $request->input('unit_id');
                    $billet->owner_id = $request->input('owner_id');
                    $billet->date_payment = $request->input('date_payment');
                    $billet->status = $request->input('status');
                    $billet->filename = $arquivo;
                    $billet->fileurl = $url;

                    $array['error'] = '';
                    // Salvar o boleto no banco de dados
                    try {
                        $billet->save();
                        Storage::delete($fileDelete);
                    } catch (Exception $e) {
                        // Tratar o erro
                        return response()->json([
                            'error' => 'Erro ao salvar boleto!',
                            'detail' => $e->getMessage(),
                            'code' => 500,
                        ], 500);
                    }
                }
            }
        } else {
            $validator = Validator::make($request->all(), [
                'title' => 'required|min:2',
                'price' => 'required',
                'date_vue' => 'required',
                'required_one_of' => [
                    'unit_id',
                    'owner_id',
                ],
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error' => $validator->errors()->first(),
                    'code' => 400,
                ], 400);
            } else {
                if ($billet) {
                    $fileDelete = $billet->filename;
                    $billet->title = $request->input('title');
                    $billet->content = $request->input('content');
                    $billet->price = $request->input('price');
                    $billet->date_vue = $request->input('date_vue');
                    $billet->unit_id = $request->input('unit_id');
                    $billet->owner_id = $request->input('owner_id');
                    $billet->date_payment = $request->input('date_payment');
                    $billet->status = $request->input('status');
                    // Salvar o boleto no banco de dados
                    try {
                        $billet->save();
                    } catch (Exception $e) {
                        // Tratar o erro
                        return response()->json([
                            'error' => 'Erro ao salvar boleto!',
                            'detail' => $e->getMessage(),
                            'code' => 500,
                        ], 500);
                    }
                }
            }
        }
        // Retornar uma resposta de sucesso
        return response()->json([
            'error' => '',
            'success' => true,
            'billetument' => $billet,
        ], 200);
    }
    /**
     * Exclui um boleto.
     *
     * @param int $id O ID do boleto a ser excluído.
     *
     * @return \Illuminate\Http\JsonResponse 
     * */

    public function delete($id)
    {
        // Buscar o aviso a ser deletado
        $billet = Billet::find($id);

        // Se o aviso não for encontrado, retornar uma mensagem de erro
        if (!$billet) {
            return response()->json([
                'error' => 'Boleto inexistente',
                'code' => 404,
            ], 404);
        }

        // Tentar deletar o aviso
        try {
            $billet->delete();
            $fileDelete = $billet->filename;
            Storage::delete($fileDelete);
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao deletar boleto!',
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
