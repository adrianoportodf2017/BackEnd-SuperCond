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
        $polls = Poll::with('options.answers')->get();


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
    // Buscar o documento pelo ID
    $poll = Poll::with('options.answers')->find($id);
    var_dump($poll);

 return response()->json([
            'poll' => $poll['0'],
            'poll2' => $poll->poll,
        ], 200);die;
    // Se a enquete não for encontrada, retornar uma mensagem de erro
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

    // Manipulação do arquivo de imagem
    $arquivo = $poll->thumb_file;
    $url = $poll->thumb;

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

        // Apagar o arquivo antigo, se existir
        if ($poll->thumb_file && Storage::exists($poll->thumb_file)) {
            Storage::delete($poll->thumb_file);
        }
    }

    // Atualizar as informações da enquete
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

 // Atualizar as opções da enquete
 $options = json_decode($request->input('options'), true);

 if (is_array($options)) {
     // Buscar as opções atuais da enquete
     $currentOptions = $poll->options; // Garantir que $currentOptions seja um array vazio se não houver opções
 
      // Encontrar as IDs das opções recebidas, excluindo as que não têm ID
    $receivedOptionIds = array_column(array_filter($options, fn($option) => isset($option['id'])), 'id');
    
    // Encontrar as opções a serem removidas
    //$optionsToRemove = array_diff($currentOptions, $receivedOptionIds);



    if (!empty($optionsToRemove)) {
        //QuestionPoll::whereIn('id', $optionsToRemove)->delete(); // Remover opções que não estão mais presentes
    }
 
          // Atualizar ou criar as opções recebidas
     foreach ($options as $option) {
         // Se o ID não estiver definido, defina-o como null
         $id = isset($option['id']) ? $option['id'] : null;
 
         // Atualizar ou criar a opção
         QuestionPoll::updateOrCreate(
             ['id' => $id], // Se $id for null, a função criará um novo registro
             ['poll_id' => $poll->id, 'title' => $option['title']]
         );
     }
    }

    // Retornar uma resposta de sucesso
    return response()->json([
        'error' => '',
        'success' => true,
        'list' => $poll,
        'optionsNotRemove' => $receivedOptionIds,
        'optionsRemove' => json_decode($currentOptions),

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
            $questions = QuestionPoll::where('poll_id', $poll->id)->get();
            foreach ($questions as $question) {
                $question->answers()->delete();
                $question->delete();
            }

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
    public function getQuestionsAll($id)
    {
        // Implemente a lógica para listar todas as perguntas de uma enquete
        $questions = QuestionPoll::where('poll_id', $id)->get();

        if (!$questions) {
            return response()->json([
                'error' => "Galeria não encontrado",
                'code' => 404,
            ], 404);
        }
        return response()->json([
            'error' => '',
            'list' => json_decode($questions),
            // Outros dados de resultado aqui...
        ], 200);
    }

    public function getQuestionById($id)
    {
        // Implemente a lógica para obter uma pergunta específica por ID
        // Implemente a lógica para obter uma enquete específica por ID

        $question = QuestionPoll::where('id', $id)->with('answers')->first();

        if (!$question) {
            return response()->json([
                'error' => "Pergunta não encontrada",
                'code' => 404,
            ], 404);
        }
        return response()->json([
            'error' => '',
            'list' => $question,
            // Outros dados de resultado aqui...
        ], 200);
    }

    public function insertQuestion(Request $request, $id)
    {
        // Implemente a lógica para criar uma nova pergunta relacionada a uma enquete

        //  return var_dump($request->file()); die;

        // Validar os dados da requisição

        $newQuestion = new QuestionPoll();


        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            'type' =>  'required|min:2',

        ]);

        // Retornar uma mensagem de erro se a validação falhar
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }

        $newQuestion->title = $request->input('title');
        $newQuestion->content = $request->input('content');
        $newQuestion->type = $request->input('type');
        $newQuestion->poll_id = $id;


        // Salvar o documento no banco de dados
        try {
            $newQuestion->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Pergunta!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }


        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $newQuestion,
        ], 201);
    }



    public function updateQuestion(Request $request, $id)
    {
        // Implemente a lógica para atualizar uma pergunta existente

        $question =  QuestionPoll::where('id', $id)->first();


        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            'type' =>  'required|min:2',

        ]);

        // Retornar uma mensagem de erro se a validação falhar
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }

        $question->title = $request->input('title');
        $question->content = $request->input('content');
        $question->type = $request->input('type');
        //$question->poll_id = $id;


        // Salvar o documento no banco de dados
        try {
            $question->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Pergunta!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }


        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $question,
        ], 201);
    }
    public function deleteQuestion($id)
    {
        // Buscar a pergunta a ser deletada
        $question = QuestionPoll::find($id);

        // Verificar se a pergunta existe
        if (!$question) {
            return response()->json(['error' => 'Pergunta Inexistente', 'code' => 404], 404);
        }

        // Excluir a pergunta e suas respostas associadas
        try {

            $question->answers()->delete();
            $question->delete();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao Deletar!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }


        return response()->json(['error' => '', 'success' => true], 200);
    }


    public function getAnswersByPoll($pollId)
    {
        // Recupere a enquete específica com suas perguntas e respostas
        $poll = Poll::with('questions.answers')
            ->find($pollId);

        // Verifique se a enquete existe
        if (!$poll) {
            return response()->json([
                'error' => 'Enquete não encontrada'
            ],  404);
        }

        // Extraia apenas as respostas da enquete
        $answers = [];
        foreach ($poll->questions as $question) {
            $answers = array_merge($answers, $question->answers->toArray());
        }

        // Retornar uma resposta de sucesso
        return response()->json([
            'error' => '',
            'list' => $answers,
        ], 200);
    }

    public function insertAnswer(Request $request, $id)
    {


        //Realizar Voto
        // Implemente a lógica para criar uma nova resposta relacionada a uma enquete 

        $newAnswer = new VotePoll();


        $validator = Validator::make($request->all(), [
            'answer' => 'required',
            'user_id' =>  'required',

        ]);

        // Retornar uma mensagem de erro se a validação falhar
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }

        $newAnswer->answer = $request->input('answer');
        $newAnswer->user_id = $request->input('user_id');
        $newAnswer->question_poll_id = $id;


        // Salvar o documento no banco de dados
        try {
            $newAnswer->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Resposta!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }


        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $newAnswer,
        ], 201);
    }
}
