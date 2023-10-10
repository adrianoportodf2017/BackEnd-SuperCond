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
        $polls = Poll::with('questions.answers')->get();
    
        return response()->json($polls);
    }

    public function getById($id)
    {
        // Implemente a lógica para obter uma enquete específica por ID
    }

    public function insert(Request $request)
    {
        // Implemente a lógica para criar uma nova enquete
    }

    public function update(Request $request, $id)
    {
        // Implemente a lógica para atualizar uma enquete existente
    }

    public function delete($id)
    {
        // Implemente a lógica para excluir uma enquete por ID
    }

    public function getQuestionsAll()
    {
        // Implemente a lógica para listar todas as perguntas de uma enquete
    }

    public function getQuestionById($id)
    {
        // Implemente a lógica para obter uma pergunta específica por ID
    }

    public function insertQuestion(Request $request)
    {
        // Implemente a lógica para criar uma nova pergunta relacionada a uma enquete
    }

    public function updateQuestion(Request $request, $id)
    {
        // Implemente a lógica para atualizar uma pergunta existente
    }

    public function deleteQuestion($id)
    {
        // Implemente a lógica para excluir uma pergunta por ID
    }

    public function getAnswersAll()
    {
        // Implemente a lógica para listar todas as respostas de uma enquete
    }

    public function getAnswerById($id)
    {
        // Implemente a lógica para obter uma resposta específica por ID
    }

    public function insertAnswer(Request $request)
    {
        // Implemente a lógica para criar uma nova resposta relacionada a uma enquete
    }

    public function updateAnswer(Request $request, $id)
    {
        // Implemente a lógica para atualizar uma resposta existente
    }

    public function deleteAnswer($id)
    {
        // Implemente a lógica para excluir uma resposta por ID
    }
}
