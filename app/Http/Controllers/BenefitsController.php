<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Benefits;
use App\Models\Midia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Exception;

class BenefitsController extends Controller
{
    public function getAll()
    {
        $polls = Benefits::all();


        // Retornar uma mensagem de erro se não houver ocorrencias
        if (!$polls) {
            return response()->json([
                'error' => 'Nenhuma Benefício Encontrado',
                'code' => 404,
            ], 404);
        }
        // Retornar uma resposta de sucesso com a lista de ocorrencias


        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $polls,
        ], 200);
    }

    public function getById($id)
    {
        // Implemente a lógica para obter uma Beneficios específica por ID

        $service = Benefits::where('id', $id)->first();

        if (!$service) {
            return response()->json([
                'error' => "Beneficios não encontrada",
                'code' => 404,
            ], 404);
        }
        return response()->json([
            'error' => '',
            'list' => $service,
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

        $newProvider = new Benefits();


        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',

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

        $newProvider->name = $request->input('name');
        $newProvider->email = $request->input('email');
        $newProvider->phone = $request->input('phone');
        $newProvider->service_type = $request->input('service_type');
        $newProvider->description = $request->input('description');
        $newProvider->address = $request->input('address');
        $newProvider->city = $request->input('city');
        $newProvider->state = $request->input('state');
        $newProvider->zip_code = $request->input('zip_code');
        $newProvider->website = $request->input('website');
        $newProvider->social_media = json_decode($request->input('social_media'), true);
        $newProvider->work_hours = $request->input('work_hours');
        $newProvider->thumb = $url;
        $newProvider->thumb_file = $arquivo;



        // Salvar o documento no banco de dados
        try {
            $newProvider->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Beneficios!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }


        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $newProvider,
        ], 201);
    }


    public function update(Request $request, $id)
    {
        // Implemente a lógica para atualizar uma Beneficios existente

        $array['id'] =  $id;
        // Buscar o documento pelo ID
        $benefit = Benefits::find($id);
        $arquivo = $benefit->thumb_file;
        $url =  $benefit->thumb;



        // Se o aviso não for encontrado, retornar uma mensagem de erro
        if (!$benefit) {
            return response()->json([
                'error' => 'Beneficios inexistente',
                'code' => 404,
            ], 404);
        }

        // Validar os dados da requisição
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',

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
            $arquivo = $request->file('thumb')->store('public/benefits/thumb');
            $url = asset(Storage::url($arquivo));
            $thumb_delete = $benefit->thumb_file;
            Storage::delete($thumb_delete);
        }


        $benefit->name = $request->input('name');
        $benefit->email = $request->input('email');
        $benefit->phone = $request->input('phone');
        $benefit->service_type = $request->input('service_type');
        $benefit->description = $request->input('description');
        $benefit->address = $request->input('address');
        $benefit->city = $request->input('city');
        $benefit->state = $request->input('state');
        $benefit->zip_code = $request->input('zip_code');
        $benefit->website = $request->input('website');
        $benefit->social_media = json_decode($request->input('social_media'), true);
        $benefit->work_hours = $request->input('work_hours');
        $benefit->thumb = $url;
        $benefit->thumb_file = $arquivo;
        // Salvar o documento no banco de dados
        try {
            $benefit->save();
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
            'list' => $benefit,
        ], 200);
    }


    public function delete($id)
    {
        // Buscar a Beneficios a ser deletada
        $benefit = Benefits::find($id);

        // Verificar se a Beneficios existe
        if (!$benefit) {
            return response()->json(['error' => 'Beneficios Inexistente', 'code' => 404], 404);
        }

        // Excluir todas as perguntas relacionadas à Beneficios e suas respostas
        try {
          
            $benefit->delete();
            $fileDelete = $benefit->thumb_file;
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
            $item = Benefits::find($id);
            $item->status = $request->input('status');
            $item->save();
            return $request->input();
        }
    }
}
