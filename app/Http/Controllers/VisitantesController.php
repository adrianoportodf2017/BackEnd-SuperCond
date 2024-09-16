<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitante;
use App\Models\Visitas;
use App\Models\Unit;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Exception;

class VisitantesController extends Controller
{



    public function getAllVisitas()
    {
        $visitas = Visitas::all();


        // Retornar uma mensagem de erro se não houver ocorrencias
        if (!$visitas) {
            return response()->json([
                'error' => 'Nenhuma Visita Encontrada',
                'code' => 404,
            ], 404);
        }
        // Retornar uma resposta de sucesso com a lista de ocorrencias


        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $visitas,
        ], 200);
    }

    public function getVisitaById($id)
    {
        // Implemente a lógica para obter uma Beneficios específica por ID

        $visita = Visitas::where('id', $id)->first();

        if (!$visita) {
            return response()->json([
                'error' => "Visita não encontrada",
                'code' => 404,
            ], 404);
        }
        return response()->json([
            'error' => '',
            'list' => $visita,
            // Outros dados de resultado aqui...
        ], 200);
    }


    /**
     * Insere um nova Beneficios.
     *
     * @param \Illuminate\Http\Request $request Os dados do documento a ser inserido.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function insert(Request $request)
    {      //  return var_dump($request->file()); die;

        // Validar os dados da requisição

        $newVisita = new Visitas();


        $validator = Validator::make($request->all(), [
            'visitante_id' => 'required|exists:visitantes,id',
            'unit_id' => 'required|exists:units,id',
            'type' => 'required|string|max:50',
            'date' => 'required|date',
            'location' => 'required|string|max:255',
            'veiculo' => 'required|string|max:255',
            'notes' => 'nullable|string',
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
            $arquivo = $request->file('thumb')->store('public/benefits/thumb');
            $url = asset(Storage::url($arquivo));
        } else {
            $arquivo = '';
            $url = '';
        }

        $newVisita->visitante_id = $request->input('visitante_id');
        $newVisita->unit_id = $request->input('unit_id');
        $newVisita->phone = $request->input('phone');
        $newVisita->type = $request->input('type');
        $newVisita->date = $request->input('date');
        $newVisita->location = $request->input('location');
        $newVisita->veiculo = $request->input('veiculo');
        $newVisita->notes = $request->input('notes');
        $newVisita->thumb = $url;
        // Salvar o documento no banco de dados
        try {
            $newVisita->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Visita!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }


        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $newVisita,
        ], 201);
    }


    public function update(Request $request, $id)
    {
        // Implemente a lógica para atualizar uma Beneficios existente

        $array['id'] =  $id;
        // Buscar o documento pelo ID
        $visita = Visitas::find($id);
        $url =  $visita->thumb;



        // Se o aviso não for encontrado, retornar uma mensagem de erro
        if (!$visita) {
            return response()->json([
                'error' => 'Visita inexistente',
                'code' => 404,
            ], 404);
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
            $arquivo = $request->file('thumb')->store('public/benefits/thumb');
            $url = asset(Storage::url($arquivo));
            $thumb_delete = $visita->thumb;
            Storage::delete($thumb_delete);
            $visita->thumb = $url;
        }


        $visita->visitante_id = $request->input('visitante_id');
        $visita->unit_id = $request->input('unit_id');
        $visita->phone = $request->input('phone');
        $visita->type = $request->input('type');
        $visita->date = $request->input('date');
        $visita->location = $request->input('location');
        $visita->veiculo = $request->input('veiculo');
        $visita->notes = $request->input('notes');

        // Salvar o documento no banco de dados
        try {
            $visita->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Beneficios!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        // Retornar uma resposta de sucesso

        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $visita,
        ], 200);
    }


    public function delete($id)
    {
        // Buscar a Beneficios a ser deletada
        $visita = Visitas::find($id);

        // Verificar se a Beneficios existe
        if (!$visita) {
            return response()->json(['error' => 'Visita Inexistente', 'code' => 404], 404);
        }

        // Excluir todas as perguntas relacionadas à Beneficios e suas respostas
        try {

            $visita->delete();
            $fileDelete = $visita->thumb;
            Storage::delete($fileDelete);

            return response()->json(['error' => '', 'success' => true], 200);
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao Deletar Beneficios',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }
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
            $item = visitas::find($id);
            $item->status = $request->input('status');
            $item->save();
            return $request->input();
        }
    }
}
