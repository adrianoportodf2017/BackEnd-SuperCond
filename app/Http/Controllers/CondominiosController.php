<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Condominios;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class CondominiosController extends Controller
{
    public function getAll()
    {
        $array = ['error' => ''];

        $docs = Condominios::all();

        $array['list'] = $docs;

        return $array;
    }

    public function insert(Request $request)
    {


        $array = ['error' => ''];
        $newDoc = new Condominios();

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2',
            'cnpj' => 'required|min:2',
         
        ]);

        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        } elseif ($request->hasfile('Thumb')) {
            if ($request->file('Thumb')->isValid()) {
                $validator = Validator::make($request->all(), [                   
                    'Thumb' => 'required|mimes:jpg,png,pdf,jpeg'
                ]);
                if ($validator->fails()) {
                    $array['error'] = $validator->errors()->first();
                    return $array;
                    exit;                    }
                
                $arquivo = $request->file('Thumb')->store('public/image/condominios');
                $newDoc->thumb = $arquivo;
                $newDoc->name = $request->input('name');
                // $newDoc->code = $request->input('code');
                 $newDoc->cnpj = $request->input('cnpj');
                 $newDoc->description = $request->input('description');
                 $newDoc->address = $request->input('address');
                 $newDoc->adress_number = $request->input('adress_number');
                 $newDoc->city = $request->input('city');
                 $newDoc->district = $request->input('district');
                 $newDoc->address_zip = $request->input('address_zip');
                 $newDoc->state = $request->input('state');
                 $newDoc->billit = $request->input('billit');
            }
        } else {
            $newDoc->name = $request->input('name');
           // $newDoc->code = $request->input('code');
            $newDoc->cnpj = $request->input('cnpj');
            $newDoc->description = $request->input('description');
            $newDoc->address = $request->input('address');
            $newDoc->adress_number = $request->input('adress_number');
            $newDoc->city = $request->input('city');
            $newDoc->district = $request->input('district');
            $newDoc->address_zip = $request->input('address_zip');
            $newDoc->state = $request->input('state');
            $newDoc->billit = $request->input('billit');
        }

        $newDoc->save();

        return $array;
    }

    public function update($id, Request $request)
    {
        $array = ['error' => ''];


        $array['id'] =  $id;
        $newDoc = Condominios::find($id);

    
        if (!$newDoc) {
            $array['error'] = 'Registro não encontrado';
            return $array;
        }
    
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:2',
            'cnpj' => 'required|min:2',
        ]);
    
        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        }
    
        if ($request->hasFile('Thumb')) {
            $validator = Validator::make($request->all(), [
                'Thumb' => 'required|mimes:jpg,png,pdf,jpeg'
            ]);
    
            if ($validator->fails()) {
                $array['error'] = $validator->errors()->first();
                return $array;
            }
    
            $thumbDelete = $newDoc->thumb;
            $arquivo = $request->file('Thumb')->store('public/image/condominios');
            $newDoc->thumb = $arquivo;
            $newDoc->name = $request->input('name');
            $newDoc->cnpj = $request->input('cnpj');
            $newDoc->description = $request->input('description');
            $newDoc->address = $request->input('address');
            $newDoc->adress_number = $request->input('adress_number');
            $newDoc->city = $request->input('city');
            $newDoc->district = $request->input('district');
            $newDoc->address_zip = $request->input('address_zip');
            $newDoc->state = $request->input('state');
            $newDoc->billit = $request->input('billit');    
            Storage::delete($thumbDelete);
        } else {
            $newDoc->name = $request->input('name');
            $newDoc->cnpj = $request->input('cnpj');
            $newDoc->description = $request->input('description');
            $newDoc->address = $request->input('address');
            $newDoc->adress_number = $request->input('adress_number');
            $newDoc->city = $request->input('city');
            $newDoc->district = $request->input('district');
            $newDoc->address_zip = $request->input('address_zip');
            $newDoc->state = $request->input('state');
            $newDoc->billit = $request->input('billit');
        }
    
        $newDoc->save();
    
        return $array;
    }
    
    
    public function delete($id)
    {
        $array = ['error' => ''];
        $item = Condominios::find($id);
        if ($item) {
            $fileDelete = $item->thumb;
            Storage::delete($fileDelete);
            Condominios::find($id)->delete();
        } else {
            $array['error'] = 'Error Ao Deletar';
            // return $array;
        }
        return $array;
    }
}
