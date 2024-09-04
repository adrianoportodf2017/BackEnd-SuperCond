<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceProvider;
use App\Models\Midia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Exception;



class ServiceProvidersController extends Controller
{
    public function getAll()
    {
        $polls = ServiceProvider::all();


        // Retornar uma mensagem de erro se não houver ocorrencias
        if (!$polls) {
            return response()->json([
                'error' => 'Nenhuma Prestador de Serviço Encontrado',
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
        // Implemente a lógica para obter uma Prestador de Serviços específica por ID

        $service = ServiceProvider::where('id', $id)->first();

        if (!$service) {
            return response()->json([
                'error' => "Prestador de Serviços não encontrada",
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
     * Insere um nova Prestador de Serviços.
     *
     * @param \Illuminate\Http\Request $request Os dados do documento a ser inserido.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function insert(Request $request)
    {      //  return var_dump($request->file()); die;

        // Validar os dados da requisição

        $newProvider = new ServiceProvider();


        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string',
            'service_type' => 'required|string',
            'description' => 'required|string',
            'address' => 'required|string',
            'website' => 'required|url',
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
            $arquivo = $request->file('thumb')->store('public/service-providers/thumb');
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
                'error' => 'Erro ao salvar Prestador de Serviços!',
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
        // Implemente a lógica para atualizar uma Prestador de Serviços existente

        $array['id'] =  $id;
        // Buscar o documento pelo ID
        $provider = ServiceProvider::find($id);
        $arquivo = $provider->thumb_file;
        $url =  $provider->thumb;



        // Se o aviso não for encontrado, retornar uma mensagem de erro
        if (!$provider) {
            return response()->json([
                'error' => 'Prestador de Serviços inexistente',
                'code' => 404,
            ], 404);
        }

        // Validar os dados da requisição
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string',
            'service_type' => 'required|string',
            //'description' => 'required|string',
            //'address' => 'required|string',
            //'website' => 'required|url',
            'thumb' => 'mimes:jpg,png,jpeg',

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
            $arquivo = $request->file('thumb')->store('public/service-providers/thumb');
            $url = asset(Storage::url($arquivo));
            $thumb_delete = $provider->thumb_file;
            Storage::delete($thumb_delete);
        }


        $provider->name = $request->input('name');
        $provider->email = $request->input('email');
        $provider->phone = $request->input('phone');
        $provider->service_type = $request->input('service_type');
        $provider->description = $request->input('description');
        $provider->address = $request->input('address');
        $provider->city = $request->input('city');
        $provider->state = $request->input('state');
        $provider->zip_code = $request->input('zip_code');
        $provider->website = $request->input('website');
        $provider->social_media = json_decode($request->input('social_media'), true);
        $provider->work_hours = $request->input('work_hours');
        $provider->thumb = $url;
        $provider->thumb_file = $arquivo;
        // Salvar o documento no banco de dados
        try {
            $provider->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Prestador de Serviços!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        // Retornar uma resposta de sucesso

        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $provider,
        ], 200);
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
            $item = ServiceProvider::find($id);
            $item->status = $request->input('status');
            $item->save();
            return $request->input();
        }
    }


    public function delete($id)
    {
        // Buscar a Prestador de Serviços a ser deletada
        $provider = ServiceProvider::find($id);

        // Verificar se a Prestador de Serviços existe
        if (!$provider) {
            return response()->json(['error' => 'Prestador de Serviços Inexistente', 'code' => 404], 404);
        }

        // Excluir todas as perguntas relacionadas à Prestador de Serviços e suas respostas
        try {
          
            $provider->delete();
            $fileDelete = $provider->thumb_file;
            Storage::delete($fileDelete);

            return response()->json(['error' => '', 'success' => true], 200);
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao Deletar Prestador de Serviços',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    }
}
