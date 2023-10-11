<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\Midia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Reservation;
use App\Models\AreaDisabledDay;
use Illuminate\Support\Facades\Storage;
use Exception;





class AreaController extends Controller
{


    public function getAll()
    {
        $areas =  Area::all();

        // Retornar uma mensagem de erro se não houver ocorrencias
        if (!$areas) {
            return response()->json([
                'error' => 'Nenhuma Área Comun Encontrada',
                'code' => 404,
            ], 404);
        }
        // Retornar uma resposta de sucesso com a lista de ocorrencias
        $result = [];
        foreach ($areas as $area) {
            $area->midias  = $area->midias;
            $result[] = $area;
        }

        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $result,
        ], 200);
    }

    /**
     * Obtém um Área comun pelo ID.
     *
     * @param int $id O ID do área a ser obtido.
     *
     * @return \App\Models\Area
     */
    public function getById($id)
    {
        $area = Area::where('id', $id)->first();

        if (!$area) {
            return response()->json([
                'error' => "Item com ID {$id} não encontrado",
                'code' => 404,
            ], 404);
        }
        $area->midias = $area->midias;
        return response()->json([
            'error' => '',
            'list' => $area,
            // Outros dados de resultado aqui...
        ], 200);
    }


    public function insert(Request $request)
    {      //  return var_dump($request->file()); die;

        // Validar os dados da requisição


        $array = ['error' => ''];
        $newArea = new Area();

        $validator = Validator::make($request->all(), [
            'allowed' => 'required',
            'title' => 'required|min:2',
            'start_time' => 'required|min:2',
            'end_time' => 'required|min:2',
            'file.*' => 'mimes:jpg,png,jpeg',
        ]);


        // Retornar uma mensagem de erro se a validação falhar
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }



        $newArea->title = $request->input('title');
        $newArea->allowed = $request->input('allowed');
        $newArea->days = $request->input('days');
        $newArea->start_time = $request->input('start_time');
        $newArea->end_time = $request->input('end_time');


        // Salvar o documento no banco de dados
        try {
            $newArea->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Área!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        // Retornar uma resposta de sucesso
        if ($request->file('file')) {
            $files = $request->file('file');
            foreach ($files as  $key) {
                $arquivo = $key->store('public/areas/' . $newArea->id);
                $url = asset(Storage::url($arquivo));
                $midia = new Midia([
                    'title' => $newArea->title,
                    'url' => $url,
                    'file' => $arquivo,
                    'status' => 'ativo', // Status da mídia
                    'type' => 'imagem', // Tipo da mídia (por exemplo, imagem, PDF, etc.)
                    'user_id' => $request->input('user_id')
                ]);
                // Associar a mídia a uma entidade (por exemplo, Document)
                // Salvar o documento no banco de dados
                $newArea->midias()->save($midia);
            }
        }
        $newArea->midias = $newArea->midias;
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $newArea,
        ], 201);
    }

    public function update($id, Request $request)
    {
        $array = ['error' => ''];
        // var_dump($_POST);die;


        $array['id'] =  $id;
        $area = Area::find($id);
        if (!$area) {
            $array['error'] = 'Registro não encontrado';
            return $array;
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
        ]);

        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        }
        $area->title = $request->input('title');
        $area->allowed = $request->input('allowed');
        $area->days = $request->input('days');
        $area->start_time = $request->input('start_time');
        $area->end_time = $request->input('end_time');


        // Salvar o documento no banco de dados
        try {
            $area->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Área comum!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        // Retornar uma resposta de sucesso
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $area,
        ], 200);
    }

    public function insertMidia($id, Request $request)
    {
        $area = Area::find($id);

        $validator = Validator::make($request->all(), [
            'file' =>  'max:10M',
            'file.*' => 'mimes:jpg,png,jpeg',
            'user_id' => 'required',
        ]);


        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'code' => 400,
            ], 400);
        }
        // Se o aviso não for encontrado, retornar uma mensagem de erro
        if (!$area) {
            return response()->json([
                'error' => 'Área inexistente',
                'code' => 404,
            ], 404);
        }
        // Verificar se o arquivo existe
        if (!$request->hasfile('file')) {
            return response()->json([
                'error' => 'Nenhum arquivo enviado',
                'code' => 400,
            ], 400);
        }

        // Verificar se o arquivo é válido
        $files = $request->file('file');

        foreach ($files as $file) {
            if (!$file->isValid()) {
                return response()->json([
                    'error' => 'O arquivo enviado não é válido',
                    'code' => 400,
                ], 400);
            }
        }
        foreach ($files as  $key) {
            $arquivo = $key->store('public/areas/' . $id);
            $url = asset(Storage::url($arquivo));
            $midia = new Midia([
                'title' => $area->title,
                'url' => $url,
                'file' => $arquivo,
                'status' => 'ativo', // Status da mídia
                'type' => 'imagem', // Tipo da mídia (por exemplo, imagem, PDF, etc.)
                'user_id' => $request->input('user_id')
            ]);
            // Associar a mídia a uma entidade (por exemplo, Document)
            // Salvar o documento no banco de dados
            try {
                $area->midias()->save($midia);
            } catch (Exception $e) {
                // Tratar o erro
                return response()->json([
                    'error' => 'Erro ao salvar Imagem na galeria!',
                    'detail' => $e->getMessage(),
                    'code' => 500,
                ], 500);
            }

            // Retornar uma resposta de sucesso
            $area->midias = $area->midias;
            return response()->json([
                'error' => '',
                'success' => true,
                'list' => $area,
            ], 200);
        }
    }

    public function deleteMidia($id, Request $request)
    {
        // Buscar o aviso a ser deletado
        $midia = Midia::find($id);



        // Se o aviso não for encontrado, retornar uma mensagem de erro
        if (!$midia) {
            return response()->json([
                'error' => 'Arquivo inexistente',
                'code' => 404,
            ], 404);
        }

        // Tentar deletar o aviso
        try {
            $midia->delete();
            $midia = $midia->file;
            Storage::delete($midia);
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao deletar Arquivo!',
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


    /**
     * Exclui um Area.
     *
     * @param int $id O ID do area a ser excluído.
     *
     * @return \Illuminate\Http\JsonResponse 
     * */

    public function delete($id)
    {
        // Buscar o aviso a ser deletado
        $area = Area::find($id);

        $midias =  $area->midias;
        foreach ($midias  as $midia) {
            $midia->delete();
            $midia = $midia->file;
            Storage::delete($midia);
        }
        // Se o aviso não for encontrado, retornar uma mensagem de erro
        if (!$area) {
            return response()->json([
                'error' => 'Área inexistente',
                'code' => 404,
            ], 404);
        }

        // Tentar deletar o aviso
        try {
            $area->delete();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao deletar A´rea!',
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




    public function getAllDates()
    {
        $array = ['error' => ''];

        $areas = Area::where('allowed', 1)->get();
        $daysHelper = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab'];

        foreach ($areas as $area) {
            $daysList = explode(',', $area['days']);
            $daysGroup = [];
            $lastDay = intval(current($daysList));
            array_shift($daysList);

            //Inserir primeiro dia
            $daysGroup[] = $daysHelper[$lastDay];

            //Inserir dias que não são sequencia
            foreach ($daysList as $day) { // 1 3 5
                if (intval($day) !== $lastDay + 1) {

                    $daysGroup[] = $daysHelper[$lastDay]; // 1
                    $daysGroup[] = $daysHelper[$day]; // 3
                }

                $lastDay = intval($day);
            }

            //Inserir ultimo dia
            $daysGroup[] = $daysHelper[end($daysList)];

            $dates = '';
            $close = 0;

            foreach ($daysGroup as $day) {
                if ($close == 0) {
                    $dates .= $day;
                } else {
                    $dates .= '-' . $day . ',';
                }

                $close = 1 - $close;
            }
            // print_r($daysGroup);
            $dates = explode(',', $dates);
            array_pop($dates);
            $start = $area['start_time'];
            $end = $area['end_time'];

            foreach ($dates as $dKey => $dValue) {
                $dates[$dKey] .= ' ' . $start . ' as ' . $end;
            }

            $array['list'][] = [
                'id_area' => $area['id'],
                'title' => $area['title'],
                'cover' => asset('storage/' . $area['cover']),
                'dates' => $dates
            ];
        }

        return $array;
    }

    public function getDisabledDates($id)
    {
        $array = ['error' => ''];

        $area = Area::find($id);

        if (!$area) {
            $array['error'] = 'Area não encontrada';
            return $array;
        }

        $allowedDays = explode(',', $area['days']);
        $offDays = [];

        for ($q = 0; $q < 7; $q++) {
            if (!in_array($q, $allowedDays)) {
                $offDays[] = $q;
            }
        }

        $start = time();
        $end = strtotime('+3 months');

        for (
            $current = $start;
            $current < $end;
            $current = strtotime('+1 day', $current)
        ) {
            $weekday = date('w', $current);
            if (in_array($weekday, $offDays)) {
                $array['list'][] = date('Y-m-d', $current);
            }
        }

        return $array;
    }

    public function getTimes(Request $request, $id)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d'
        ]);

        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        $date = $request->input('date');
        $area = Area::find($id);

        if (!$area) {
            $array['error'] = 'Area não encontrada';
            return $array;
        }

        $isDisabled = AreaDisabledDay::where('unit_id', $id)->where('day', $date)->first();
        if ($isDisabled) {
            $array['error'] = 'Dia indisponível!';
            return $array;
        }

        $allowedDays = explode(',', $area['days']);
        $weekday = date('w', strtotime($date));

        if (!in_array($weekday, $allowedDays)) {
            $array['error'] = 'Area não está em funcionamento neste dia!';
            return $array;
        }

        $start = strtotime($area['start_time']);
        $end = strtotime($area['end_time']);
        $timeList = [];

        for (
            $lastTime = $start;
            $lastTime < $end;
            $lastTime = strtotime('+1 hour', $lastTime)
        ) {
            $timeList[] = [
                'id' => date('H:i:s', $lastTime),
                'title' => date('H:i', $lastTime) . ' ' . date('H:i', strtotime('+1 hour', $lastTime))
            ];
        }

        $toRemove = [];
        $reservations = Reservation::where('unit_id', $id)->whereBetween('reservation_date', [
            $date . ' 00:00:00',
            $date . ' 23:59:59'
        ])->get();

        foreach ($reservations as $reservation) {
            $toRemove[] = date('H:i:s', strtotime($reservation['reservation_date']));
        }

        foreach ($timeList as $time) {
            if (!in_array($time['id'], $toRemove)) {
                $array['list'][] = $time;
            }
        }


        return $array;
    }
}
