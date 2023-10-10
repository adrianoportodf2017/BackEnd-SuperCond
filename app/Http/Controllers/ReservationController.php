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
        $reservations = Reservation::all();
         // Retornar uma mensagem de erro se a unidade não for encontrada
         if (!$reservations) {
            return response()->json([
                'error' => 'Nenhum Reserva não encontrada',
                'code' => 404,
            ], 404);
        }
    
        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $reservations,
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
    
        // Validar os dados da request
        $validator = Validator::make($request->all(), [
            'reservation_date' => 'required',
            'unit_id' => 'required',
            'id_area' => 'required',
        ]);
    
        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return response()->json($array, 400);
        }
    
        // Criar uma nova reserva
        $reservation = new Reservation();
        $reservation->id_area = $request->input('id_area');
        $reservation->unit_id = $request->input('unit_id');
        $reservation->reservation_date = $request->input('reservation_date');
        $reservation->save();
    
        // Retornar uma resposta de sucesso com os dados da reserva
        return response()->json([
            'error' => '',
            'success' => true,
            'data' => [
                'id' => $reservation->id,
                'id_area' => $reservation->id_area,
                'unit_id' => $reservation->unit_id,
                'reservation_date' => $reservation->reservation_date,
            ],
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
            $array['error'] = 'Reserva inexistente';
            // return $array;
        }
        return $array;
    }

}
