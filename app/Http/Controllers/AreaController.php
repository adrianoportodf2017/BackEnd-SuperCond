<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;
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

        $array = ['error' => ''];
        $areas = Area::all();
        // $areas['cover'] = asset(Storage::url($areas['cover']));
        $array['list'] = $areas;
        return $array;;
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
        $area->photos_array = json_decode($area->photos);
        return response()->json([
            'error' => '',
            'list' => json_decode($area),
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

        // Verificar se o arquivo existe
        if ($request->hasfile('file')) {            
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
            $cont = '0';
            $file = [];
            foreach ($files as  $key) {
                $arquivo = $key->store('public/lostAndFound/' . $request->input('owner_id'));
                $file[$cont] = $arquivo;
                $cont++;
            }
            $json_files = json_encode($file);
        }else{
            $json_files = '';

        }

            $newArea->photos = $json_files;
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
        return response()->json([
            'error' => '',
            'success' => true,
            'document' => $newArea,
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

        if ($request->hasFile('cover')) {
            $validator = Validator::make($request->all(), [
                'cover' => 'required|mimes:jpg,png,pdf,jpeg'
            ]);

            if ($validator->fails()) {
                $array['error'] = $validator->errors()->first();
                return $array;
            }

            $coverDelete = $area->cover;
            $arquivo = $request->file('cover')->store('public/image/areas');
            $url = asset(Storage::url($arquivo));
            $area->cover = $url;
            $area->title = $request->input('title');
            $area->allowed = $request->input('allowed');
            $area->days = $request->input('days');
            $area->start_time = $request->input('start_time');
            $area->end_time = $request->input('end_time');
            // Converta a URL em um caminho relativo ao sistema de arquivos
            $relativePath = str_replace(asset(''), '', $coverDelete);
            $relativePath = str_replace('storage', '', $relativePath);
            // Use o caminho relativo para excluir o arquivo
            //var_dump($relativePath);die;
            //   Storage::delete('public/image/areas/G4RCjcZN9gMoDxvZ7BsSPkV9Egl1smtyKrNO2tVe.png');


            Storage::delete('public' . $relativePath);
        } else {
            $area->title = $request->input('title');
            $area->allowed = $request->input('allowed');
            $area->days = $request->input('days');
            $area->start_time = $request->input('start_time');
            $area->end_time = $request->input('end_time');
        }

        $area->save();

        return $array;
    }

    public function delete($id)
    {
        $array = ['error' => ''];
        $item = Area::find($id);
        if ($item) {
            $fileDelete = $item->cover;
            // Converta a URL em um caminho relativo ao sistema de arquivos
            $relativePath = str_replace(asset(''), '', $fileDelete);
            $relativePath = str_replace('storage', '', $relativePath);
            // Use o caminho relativo para excluir o arquivo
            Storage::delete('public' . $relativePath);
            Area::find($id)->delete();
        } else {
            $array['error'] = 'Error Ao Deletar';
            // return $array;
        }
        return $array;
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
