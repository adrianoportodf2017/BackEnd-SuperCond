<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\UnitPeople;
use App\Models\UnitVehicle;
use App\Models\UnitPet;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Exception;


class UnitController extends Controller
{

    public function getAll()
    {
        // Buscar todas as unidades
        $units = Unit::join('users', 'units.owner_id', '=', 'users.id')
            ->select('units.*', 'users.name as name_owner', 'users.email', 'users.phone')
            ->get();

        // Retornar uma mensagem de erro se não houver unidades
        if (!$units) {
            return response()->json([
                'error' => 'Nenhuma unidade encontrada',
                'code' => 404,
            ], 404);
        }

        // Retornar uma resposta de sucesso com a lista de unidades
        return response()->json([
            'error' => '', 

            'success' => true,
            'list' => $units,
        ], 200);
    }

    public function getById($id)
    {
        // Buscar a unidade pelo ID
        $unit = Unit::find($id);

        // Retornar uma mensagem de erro se a unidade não for encontrada
        if (!$unit) {
            return response()->json([
                'error' => 'Unidade não encontrada',
                'code' => 404,
            ], 404);
        }

        // Buscar as pessoas, veículos e animais da unidade
        $peoples = UnitPeople::where('unit_id', $id)->get();
        $vehicles = UnitVehicle::where('unit_id', $id)->get();
        $pets = UnitPet::where('unit_id', $id)->get();

        // Formatar as datas de nascimento das pessoas
        foreach ($peoples as $pKey => $pValue) {
            $peoples[$pKey]['birthdate'] = date('d/m/Y', strtotime($pValue['birthdate']));
        }

        // Retornar uma resposta de sucesso com os dados da unidade
        return response()->json([
            'error' => '', 
            'success' => true,
            'unit' => $unit,
            'peoples' => $peoples,
            'vehicles' => $vehicles,
            'pets' => $pets,
        ], 200);
    }


    public function getUnitByOwner($id)
    {

        // Buscar as unidades do proprietário pelo ID
        $units = Unit::where('owner_id', $id)->get();

        // Retornar uma mensagem de erro se não houver unidades para o proprietário
        if (!$units) {
            return response()->json([
                'error' => 'Nenhuma unidade encontrada para este proprietário',
                'code' => 404,
            ], 404);
        }

        // Criar um array para armazenar os dados das unidades
        $array = [];

        // Iterar sobre as unidades e adicionar os dados ao array
        foreach ($units as $unit) {
            $array[] = [
                'id' => $unit->id,
                'name' => $unit->name,
                'owner_id' => $unit->owner_id,
                'address' => $unit->address,
                'peoples' => UnitPeople::where('unit_id', $unit->id)->get(),
                'vehicles' => UnitVehicle::where('unit_id', $unit->id)->get(),
                'pets' => UnitPet::where('unit_id', $unit->id)->get(),
            ];
        }

        // Retornar uma resposta de sucesso com os dados das unidades
        return response()->json([
            'error' => '', 
            'success' => true,
            'list' => $array,
        ], 200);
    }


    public function insert(Request $request)
    {
        // Validar os dados da requisição
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'owner_id' => 'required',
        ]);

        // Retornar uma mensagem de erro se a validação falhar
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }

        // Criar uma nova unidade
        $newUnit = new Unit();
        $newUnit->name = $request->input('name');
        $newUnit->owner_id = $request->input('owner_id');
        $newUnit->address = $request->input('address');

        // Salvar a unidade no banco de dados
        try {
            $newUnit->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao salvar unidade!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        // Retornar uma resposta de sucesso
        return response()->json([
            'success' => true,
            'unit' => $newUnit,
        ], 201);
    }

    public function updateUnit(Request $request, $id)
    {
        // Buscar a unidade pelo ID
        $unit = Unit::find($id);

        // Retornar uma mensagem de erro se a unidade não for encontrada
        if (!$unit) {
            return response()->json([
                'error' => 'Unidade não encontrada',
                'code' => 404,
            ], 404);
        }

        // Validar os dados da requisição
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'owner_id' => 'required',
        ]);

        // Retornar uma mensagem de erro se a validação falhar
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }

        // Atualizar os dados da unidade
        $unit->name = $request->input('name');
        $unit->owner_id = $request->input('owner_id');
        $unit->address = $request->input('address');


        // Salvar a unidade no banco de dados
        try {
            $unit->save();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao atualizar unidade!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        // Retornar uma resposta de sucesso
        return response()->json([
            'error' => '', 
            'success' => true,
            'unit' => $unit,
        ], 200);
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
            $newPerson->unit_id = $id;
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
            $newVehicle->unit_id = $id;
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
            $newPet->unit_id = $id;
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
                ->where('unit_id', $id)
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
                ->where('unit_id', $id)
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
                ->where('unit_id', $id)
                ->delete();
        } else {
            $array['error'] = 'ID inexistente';
            return $array;
        }

        return $array;
    }

    public function delete($id)
    {
        // Buscar a unidade pelo ID
        $unit = Unit::find($id);
    
        // Retornar uma mensagem de erro se a unidade não for encontrada
        if (!$unit) {
            return response()->json([
                'error' => 'Unidade não encontrada',
                'code' => 404,
            ], 404);
        }
    
        // Deletar a unidade do banco de dados
        try {
            $unit->delete();
        } catch (Exception $e) {
            // Tratar o erro
            return response()->json([
                'error' => 'Erro ao deletar unidade!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }
    
        // Deletar as pessoas, veículos e animais da unidade
        UnitPet::where('unit_id', $id)->delete();
        UnitVehicle::where('unit_id', $id)->delete();
        UnitPeople::where('unit_id', $id)->delete();
    
        // Retornar uma resposta de sucesso
        return response()->json([
            'error' => '', 
            'success' => true,
        ], 200);
    }
}
