<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Poll;
use App\Models\QuestionPoll;
use App\Models\VotePoll;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Exception;



class PollsController extends Controller
{
    public function getAll()
    {
        $polls = Poll::all();


        // Retornar uma mensagem de erro se não houver ocorrencias
        if (!$polls) {
            return response()->json([
                'error' => 'Nenhuma Enquete encontrada',
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
        // Implemente a lógica para obter uma enquete específica por ID

        $poll = Poll::where('id', $id)->with('questions.answers')->first();

        if (!$poll) {
            return response()->json([
                'error' => "Enquete não encontrada",
                'code' => 404,
            ], 404);
        }
        return response()->json([
            'error' => '',
            'list' => $poll,
            // Outros dados de resultado aqui...
        ], 200);
    }


    /**
     * Insere um nova Enquete.
     *
     * @param \Illuminate\Http\Request $request Os dados do documento a ser inserido.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function insert(Request $request)
    {      //  return var_dump($request->file()); die;

        // Validar os dados da requisição

        $newPoll = new Poll();


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
            $arquivo = $request->file('thumb')->store('public/poll/thumb');
            $url = asset(Storage::url($arquivo));
        } else {
            $arquivo = '';
            $url = '';
        }

        $newPoll->title = $request->input('title');
        $newPoll->content = $request->input('content');
        $newPoll->thumb = $url;
        $newPoll->thumb_file = $arquivo;
        $newPoll->status = $request->input('status');
        $newPoll->type = $request->input('type');

        $newPoll->date_start = $request->input('date_start');
        $newPoll->date_expiration = $request->input('date_expiration');

        // Salvar o documento no banco de dados
        try {
            $newPoll->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Enquete!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }


        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $newPoll,
        ], 201);
    }


    public function update(Request $request, $id)
    {
        // Implemente a lógica para atualizar uma enquete existente

        $array['id'] =  $id;
        // Buscar o documento pelo ID
        $poll = Poll::find($id);
        $arquivo = $poll->thumb_file;
        $url =  $poll->thumb;



        // Se o aviso não for encontrado, retornar uma mensagem de erro
        if (!$poll) {
            return response()->json([
                'error' => 'Enquete inexistente',
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
            $arquivo = $request->file('thumb')->store('public/poll/thumb');
            $url = asset(Storage::url($arquivo));
            $thumb_delete = $poll->thumb_file;
            Storage::delete($thumb_delete);
        }


        $poll->title = $request->input('title');
        $poll->content = $request->input('content');
        $poll->thumb_file = $arquivo;
        $poll->thumb = $url;
        $poll->status = $request->input('status');
        $poll->type = $request->input('type');
        $poll->date_start = $request->input('date_start');
        $poll->date_expiration = $request->input('date_expiration');
        // Salvar o documento no banco de dados
        try {
            $poll->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Enquete!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        // Retornar uma resposta de sucesso

        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $poll,
        ], 200);
    }


    public function delete($id)
    {
        // Buscar a enquete a ser deletada
        $poll = Poll::find($id);

        // Verificar se a enquete existe
        if (!$poll) {
            return response()->json(['error' => 'Enquete Inexistente', 'code' => 404], 404);
        }

        // Excluir todas as perguntas relacionadas à enquete e suas respostas
        try {
          
            $poll->delete();
            $fileDelete = $poll->thumb_file;
            Storage::delete($fileDelete);

            return response()->json(['error' => '', 'success' => true], 200);
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao Deletar Enquete',
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
            $item = Poll::find($id);
            $item->status = $request->input('status');
            $item->save();
            return $request->input();
        }
    }
   
}
