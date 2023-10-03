<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Area;
use App\Models\AreaDisabledDay;
use App\Models\Unit;
use App\Models\Reservation;

class ReservationController extends Controller
{
    public function getAll()
    {
        $array = ['error' => ''];
        $reservations = Reservation::select('reservations.*', 'units.name as name_unit', 'areas.title as name_area')
            ->join('units', 'units.id', '=', 'reservations.unit_id')
            ->join('users', 'users.id', '=', 'units.owner_id')
            ->join('areas', 'areas.id', '=', 'reservations.id_area')
            ->get();
        $array['list'] = $reservations;
        return $array;
    }
    public function insert(Request $request)
    {
        $array = ['error' => ''];
        // echo 'teste';
       // var_dump($request->input());
        $validator = Validator::make($request->all(), [
            'reservation_date' => 'required',
            'unit_id' => 'required',
            'id_area' => 'required',
        ]);

        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        //  $user = Auth::user();
        $dates = explode(' ', $request->input('reservation_date'));

        $date = $dates['0'];
        $time = $dates['1'];
        $property = $request->input('unit_id');
        $idArea = $request->input('id_area');

        $area = Area::find($idArea);
        $unit = Unit::where('id', $property)->first();

        if (!$area && !$unit) {
            $array['error'] = 'Dados inválidos';
            return $array;
        }

        $timeRes = strtotime($time);
        $start = strtotime($area['start_time']);
        $end = strtotime($area['end_time']);

        if ($timeRes < $start || $timeRes > $end) {
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

        $dateRes = $date . ' ' . $time;
        $isReserved = Reservation::where('id_area', $idArea)->where('reservation_date', $dateRes)->first();
        if ($isReserved) {
            $array['error'] = 'Horário já reservado neste dia';
            return $array;
        }

        $newRes = new Reservation();
        $newRes->id_area = $idArea;
        $newRes->unit_id = $property;
        $newRes->reservation_date = $dateRes;
        $newRes->save();

        $array['list'][] = [
            'id_area' => $area['id'],
            'title' => $area['title'],
            'cover' => asset('storage/' . $area['cover']),
            'date' => $dateRes . ' as ' . date('H:i', strtotime('+1 hour', strtotime($dateRes)))
        ];

        return $array;
    }
    // $reservation = Reservation::create($array);
}
