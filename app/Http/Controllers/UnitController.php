<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\UnitPeople;
use App\Models\UnitVehicle;
use App\Models\UnitPet;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{

    public function getAll()
    {
        $array = ['error' => ''];

        $units = Unit::join('users', 'units.id_owner', '=', 'users.id')
            ->select('units.*', 'users.name as name_owner', 'users.email', 'users.phone')
            ->get();

        $array['list'] = $units;

        return $array;
    }

    public function getById($id)
    {
        $array = ['error' => ''];

        $unit = Unit::find($id);

        if ($unit) {
            $peoples = UnitPeople::where('id_unit', $id)->get();
            $vehicles = UnitVehicle::where('id_unit', $id)->get();
            $pets = UnitPet::where('id_unit', $id)->get();

            foreach ($peoples as $pKey => $pValue) {
                $peoples[$pKey]['birthdate'] = date('d/m/Y', strtotime($pValue['birthdate']));
            }

            $array['unit'] = $unit;
            $array['peoples'] = $peoples;
            $array['vehicles'] = $vehicles;
            $array['pets'] = $pets;
        } else {
            $array['error'] = 'Propriedade inexistente';
            return $array;
        }

        return $array;
    }


    public function getUnitByOwner($id)
    {

        $units = Unit::where('id_owner', $id)->get();
        if ($units) {
            $cont = '0';
            foreach ($units as $unit) {
                $cont = $cont + 1;

                $peoples = UnitPeople::where('id_unit', $unit->id)->get();
                $vehicles = UnitVehicle::where('id_unit', $unit->id)->get();
                $pets = UnitPet::where('id_unit', $unit->id)->get();

                foreach ($peoples as $pKey => $pValue) {
                    $peoples[$pKey]['birthdate'] = date('d/m/Y', strtotime($pValue['birthdate']));
                }

                $array[$cont] = $unit;
                $array[$cont]['peoples'] = $peoples;
                $array[$cont]['vehicles'] = $vehicles;
                $array[$cont]['pets'] = $pets;
            }
        } else {
            $array['error'] = 'Propriedade inexistente';
            return $array;
        }
        return $array;
    }

    public function insert(Request $request)
    {
        $array = ['error' => ''];
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'id_owner' => 'required',
        ]);
        if (!$validator->fails()) {
            $name = $request->input('name');
            $id_owner = $request->input('id_owner');
            $address = $request->input('address');

            $newUser = new Unit();
            $newUser->name = $name;
            $newUser->id_owner = $id_owner;
            $newUser->address = $address;
            $newUser->save();
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }
        return $array;
    }

    public function updateUnit(Request $request, $id)
    {
        $data = $request;
        $unit = Unit::find($id);
        if ($unit) {
            $unit->name = $data->input('name');
            $unit->id_owner = $data->input('id_owner');
            $unit->save();
        } else {

            return response()->json(['error' => 'Não Foi Possível salvar esta unidade']);
        }
        return response()->json(['error' => '']);
    }





    public function addPerson($id, Request $request)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'birthdate' => 'required|date'
        ]);

        if (!$validator->fails()) {
            $name = $request->input('name');
            $birthdate = $request->input('birthdate');

            $newPerson = new UnitPeople();
            $newPerson->id_unit = $id;
            $newPerson->name = $name;
            $newPerson->birthdate = $birthdate;
            $newPerson->save();
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function addVehicle($id, Request $request)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'color' => 'required',
            'plate' => 'required'
        ]);

        if (!$validator->fails()) {
            $title = $request->input('title');
            $color = $request->input('color');
            $plate = $request->input('plate');

            $newVehicle = new UnitVehicle();
            $newVehicle->id_unit = $id;
            $newVehicle->title = $title;
            $newVehicle->color = $color;
            $newVehicle->plate = $plate;
            $newVehicle->save();
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }

    public function addPet($id, Request $request)
    {
        $array = ['error' => ''];

        $validator = Validator::make($request->all(), [
            'race' => 'required',
            'name' => 'required',
        ]);

        if (!$validator->fails()) {
            $name = $request->input('name');
            $race = $request->input('race');

            $newPet = new UnitPet();
            $newPet->id_unit = $id;
            $newPet->name = $name;
            $newPet->race = $race;
            $newPet->save();
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }


    public function removePerson($id, Request $request)
    {
        $array = ['error' => ''];

        $idItem =  $request->input('id');

        if ($idItem) {
            UnitPeople::where('id', $idItem)
                ->where('id_unit', $id)
                ->delete();
        } else {
            $array['error'] = 'ID inexistente';
            return $array;
        }

        return $array;
    }

    public function removeVehicle($id, Request $request)
    {
        $array = ['error' => ''];

        $idItem =  $request->input('id');

        if ($idItem) {
            UnitVehicle::where('id', $idItem)
                ->where('id_unit', $id)
                ->delete();
        } else {
            $array['error'] = 'ID inexistente';
            return $array;
        }

        return $array;
    }

    public function removePet($id, Request $request)
    {
        $array = ['error' => ''];

        $idItem =  $request->input('id');

        if ($idItem) {
            UnitPet::where('id', $idItem)
                ->where('id_unit', $id)
                ->delete();
        } else {
            $array['error'] = 'ID inexistente';
            return $array;
        }

        return $array;
    }

    public function delete($id)
    {
        $array = ['error' => ''];
        $item = Unit::find($id);
        if ($item) {
            Unit::find($id)->delete();
            UnitPet::where('id_unit', $id)->delete();
            UnitVehicle::where('id_unit', $id)->delete();
            UnitPeople::where('id_unit', $id)->delete();
        } else {
            $array['error'] = 'Aviso inexistente';
            // return $array;
        }
        return $array;
    }
}
