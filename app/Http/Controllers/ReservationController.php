<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;



use App\Models\Area;
use App\Models\AreaDisabledDay;
use App\Models\Unit;
use App\Models\Reservation;
use Carbon\Carbon;

use Exception;


class ReservationController extends Controller
{

    public function getAll()
    {
        $reservations = Reservation::select('reservations.*', 'units.name as name_unit', 'areas.title as name_area')
            ->join('units', 'units.id', '=', 'reservations.unit_id')
            ->join('users', 'users.id', '=', 'units.owner_id')
            ->join('areas', 'areas.id', '=', 'reservations.area_id')
            ->get();

        // Verifica se existem reservas
        if (!$reservations) {
            return response()->json([
                'error' => 'Nenhuma reserva encontrada',
                'code' => 404,
            ], 404);
        }

        $formattedReservations = [];
        foreach ($reservations as $reservation) {
            // Formata start_time e end_time como datas legíveis
            // Formata start_time e end_time como datas legíveis
            $reservation->start_time = Carbon::createFromTimestamp($reservation->start_time)->format('d/m/Y H:i');
            $reservation->end_time = Carbon::createFromTimestamp($reservation->end_time)->format('H:i');
            $reservation->date_reservation = $reservation->start_time . ' às ' .  $reservation->end_time;


            $formattedReservations[] = $reservation;
        }

        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $formattedReservations,
        ], 200);
    }

    public function getById($id)
    {
        // Buscar a unidade pelo ID
        $reservation = Reservation::find($id);

        // Retornar uma mensagem de erro se a unidade não for encontrada
        if (!$reservation) {
            return response()->json([
                'error' => 'Reserva não encontrada',
                'code' => 404,
            ], 404);
        }

        // Buscar unidades relacionada a reserva
        $unit = Unit::where('unit_id', $id)->get();


        // Retornar uma resposta de sucesso com os dados da unidade
        return response()->json([
            'error' => '',
            'success' => true,
            'unit' => $unit,
        ], 200);
    }

    public function insert(Request $request)
    {
        $array = ['error' => ''];
        // echo 'teste';
        // var_dump($request->input());
        $validator = Validator::make($request->all(), [
            'reservation_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'unit_id' => 'required',
            'area_id' => 'required',
        ]);

        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        $date =  $request->input('reservation_date');
        $property = $request->input('unit_id');
        $idArea = $request->input('area_id');
        $start_time =  strtotime($date . ' ' . $request->input('start_time') . ':00');
        $end_time = strtotime($date . ' ' . $request->input('end_time') . ':00');
        if ($end_time < $start_time) {
            $end_time = strtotime('+1 day', $end_time);
        }


        $area = Area::find($idArea);
        $unit = Unit::where('id', $property)->first();

        if (!$area && !$unit) {
            $array['error'] = 'Dados inválidos';
            return $array;
        }

        $start_time_convert = strtotime(date('H:i', $start_time)); // valor vindo do form
        $end_time_convert =   strtotime(date('H:i', $end_time)); // valor vindo do form
        $start = strtotime($area['start_time']);
        $end = strtotime($area['end_time']);

        if ($start_time_convert < $start || $end_time_convert > $end) {
            $array['error'] = 'Horário de reserva inválido';
            return $array;
        }

        $allowedDays = explode(',', $area['days']);
        $weekday = date('w', strtotime($date));

        if (!in_array($weekday, $allowedDays)) {
            $array['error'] = 'Dia inválido';
            return $array;
        }

        /* $isDisabled = AreaDisabledDay::where('id_area', $idArea)->where('day', $date)->first();
        if ($isDisabled) {
            $array['error'] = 'Dia indiponivel';
            return $array;
        }*/


        $isReserveds = Reservation::where('area_id', $idArea)->where('reservation_date', $date)->get();



        if ($isReserveds) {
            $start_time_unix = strtotime($start_time);
            $end_time_unix = strtotime($end_time);



            foreach ($isReserveds as $isReserved) {


                if ($start_time >= $isReserved->start_time && $start_time <= $isReserved->end_time) {

                    return response()->json([
                        'error' => 'Horário já reservado neste dia',
                        'detail' => $isReserved,
                        'code' => 402,
                    ], 500);
                }
            }
        }

        $newRes = new Reservation();
        $newRes->area_id = $idArea;
        $newRes->unit_id = $property;
        $newRes->reservation_date = $date;
        $newRes->start_time = $start_time;
        $newRes->end_time = $end_time;
        // Salvar o documento no banco de dados
        try {
            $newRes->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Reserva!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        // Retornar uma resposta de sucesso com os dados da reserva
        return response()->json([
            'error' => '',
            'success' => true,
            'data' => [
                'id' => $newRes->id,
                'area_id' => $newRes->area_id,
                'unit_id' => $newRes->unit_id,
                'reservation_date' => $newRes->reservation_date,
                'start_time' => $newRes->start_time,
                'end_time' => $newRes->end_time,


            ],
            'list' =>  [
                'id_area' => $area['id'],
                'title' => $area['title'],
                'date' => $date . ' as ' . date('H:i', strtotime($start_time)) . ' até às ' . date('H:i', strtotime($end_time))
            ]
        ], 201);
    }




    public function update(Request $request, $id)
    {
        $array = ['error' => ''];
        $reservation = Reservation::find($id);

        // Verifica se existem reservas
        if (!$reservation) {
            return response()->json([
                'error' => 'Nenhuma reserva encontrada',
                'code' => 404,
            ], 404);
        }

        // echo 'teste';
        // var_dump($request->input());
        $validator = Validator::make($request->all(), [
            'reservation_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'unit_id' => 'required',
            'area_id' => 'required',
        ]);

        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        $date =  $request->input('reservation_date');
        $property = $request->input('unit_id');
        $idArea = $request->input('area_id');
        $start_time =  strtotime($date . ' ' . $request->input('start_time') . ':00');
        $end_time = strtotime($date . ' ' . $request->input('end_time') . ':00');
        if ($end_time < $start_time) {
            $end_time = strtotime('+1 day', $end_time);
        }


        $area = Area::find($idArea);
        $unit = Unit::where('id', $property)->first();

        if (!$area && !$unit) {
            $array['error'] = 'Dados inválidos';
            return $array;
        }

        $start_time_convert = strtotime(date('H:i', $start_time)); // valor vindo do form
        $end_time_convert =   strtotime(date('H:i', $end_time)); // valor vindo do form
        $start = strtotime($area['start_time']);
        $end = strtotime($area['end_time']);

        if ($start_time_convert < $start || $end_time_convert > $end) {
            $array['error'] = 'Horário de reserva inválido';
            return $array;
        }

        $allowedDays = explode(',', $area['days']);
        $weekday = date('w', strtotime($date));

        if (!in_array($weekday, $allowedDays)) {
            $array['error'] = 'Dia inválido';
            return $array;
        }

        /* $isDisabled = AreaDisabledDay::where('id_area', $idArea)->where('day', $date)->first();
        if ($isDisabled) {
            $array['error'] = 'Dia indiponivel';
            return $array;
        }*/


        $isReserveds = Reservation::where('area_id', $idArea)->where('reservation_date', $date)->get();



        if ($isReserveds) {
            $start_time_unix = strtotime($start_time);
            $end_time_unix = strtotime($end_time);



            foreach ($isReserveds as $isReserved) {

                if ($start_time >= $isReserved->start_time && $start_time <= $isReserved->end_time) {
                    if ($isReserved->id == $id) {
                    } else {
                        return response()->json([
                            'error' => 'Horário já reservado neste dia',
                            'detail' => $isReserved,
                            'code' => 402,
                        ], 500);
                    }
                }
            }
        }

        $reservation->area_id = $idArea;
        $reservation->unit_id = $property;
        $reservation->reservation_date = $date;
        $reservation->start_time = $start_time;
        $reservation->end_time = $end_time;
        // Salvar o documento no banco de dados
        try {
            $reservation->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar Reserva!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        // Retornar uma resposta de sucesso com os dados da reserva
        return response()->json([
            'error' => '',
            'success' => true,
            'data' => [
                'id' => $reservation->id,
                'area_id' => $reservation->area_id,
                'unit_id' => $reservation->unit_id,
                'reservation_date' => $reservation->reservation_date,
                'start_time' => $reservation->start_time,
                'end_time' => $reservation->end_time,


            ],
            'list' =>  [
                'id_area' => $area['id'],
                'title' => $area['title'],
                'date' => $date . ' as ' . date('H:i', strtotime($start_time)) . ' até às ' . date('H:i', strtotime($end_time))
            ]
        ], 201);
    }





    // $reservation = Reservation::create($array);


    public function delete($id)
    {
        $array = ['error' => ''];
        $item = Reservation::find($id);
        if ($item) {
            Reservation::find($id)->delete();
        } else {
            return response()->json([
                'error' => 'Reserva inexistente',
                'success' => '',
    
                
            ], 500);
            // return $array;
        }
        return response()->json([
            'error' => '',
            'success' => 'Reserva deletado com sucesso',            
        ], 201);
    }
}
