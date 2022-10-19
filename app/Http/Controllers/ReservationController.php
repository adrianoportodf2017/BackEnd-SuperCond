<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\Reservation;

class ReservationController extends Controller
{
    public function getAll()
    {
        $array = ['error' => ''];     
        $reservations = Reservation::select('reservations.*', 'units.name as name_unit', 'areas.title as name_area')
        ->join('units', 'units.id', '=', 'reservations.id_unit')
        ->join('users', 'users.id', '=', 'units.id_owner')
        ->join('areas', 'areas.id', '=', 'reservations.id_area')
        ->get();
        $array['list'] = $reservations;
        return $array;
    }
}
