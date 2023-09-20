<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assembleia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class AssembleiaController extends Controller
{
    public function getAll()
    {
        $array = ['error' => ''];
        $docs = Assembleia::all();
        $array['list'] = $docs;
        return $array;
    }

    public function insert(Request $request)
    {
        $array = ['error' => ''];
        //   var_dump($request);

        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            //'file' => 'required|mimes:jpg,png,pdf,jpeg'
        ]);
        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        } elseif ($request->hasfile('thumb')) {

            $validator = Validator::make($request->all(), [
                'thumb' => 'required|mimes:jpg,png,jpeg'
            ]);

            if ($request->file('thumb')->isValid()) {
                $arquivo = $request->file('thumb')->store('public/image/assembleias');
                $url = asset(Storage::url($arquivo));
                $title = $request->input('title');
                $content = $request->input('content');
                $status = $request->input('status');
                $order = $request->input('order');
                $newAssembleia = new Assembleia();
                $newAssembleia->title = $title;
                $newAssembleia->content = $content;
                $newAssembleia->status = $status;
                $newAssembleia->order = $order;
                $newAssembleia->thumb = $url;
                $newAssembleia->created_at = date('Y-m-d H:m:s');
                $newAssembleia->save();
            } else {

                $array['error'] = $validator->errors()->first();
            }
        } else {

            $title = $request->input('title');
            $content = $request->input('content');
            $status = $request->input('status');
            $order = $request->input('order');
            $newAssembleia = new Assembleia();
            $newAssembleia->title = $title;
            $newAssembleia->content = $content;
            $newAssembleia->status = $status;
            $newAssembleia->order = $order;
            $newAssembleia->created_at = date('Y-m-d H:m:s');
            $newAssembleia->save();
        }
        return $array;
    }
    public function update($id, Request $request)
    {
        $array = ['error' => ''];
        // var_dump($_POST);die;
        $array['id'] =  $id;
        $assembleia = Assembleia::find($id);
        if (!$assembleia) {
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

        if ($request->hasFile('thumb')) {
            $validator = Validator::make($request->all(), [
                'thumb' => 'required|mimes:jpg,png,pdf,jpeg'
            ]);

            if ($validator->fails()) {
                $array['error'] = $validator->errors()->first();
                return $array;
            }

            $thumbDelete = $assembleia->thumb;
            $arquivo = $request->file('thumb')->store('public/image/assembleias');
            $url = asset(Storage::url($arquivo));
            $title = $request->input('title');
            $content = $request->input('content');
            $status = $request->input('status');
            $order = $request->input('order');
            $assembleia->title = $title;
            $assembleia->content = $content;
            $assembleia->status = $status;
            $assembleia->order = $order;
            $assembleia->thumb = $url;
            $assembleia->save();
            // Converta a URL em um caminho relativo ao sistema de arquivos
            $relativePath = str_replace(asset(''), '', $thumbDelete);
            $relativePath = str_replace('storage', '', $relativePath);
            // Use o caminho relativo para excluir o arquivo
            //var_dump($relativePath);die;
            //   Storage::delete('public/image/areas/G4RCjcZN9gMoDxvZ7BsSPkV9Egl1smtyKrNO2tVe.png');
           Storage::delete('public' . $relativePath);
        } else {
            $title = $request->input('title');
            $content = $request->input('content');
            $status = $request->input('status');
            $order = $request->input('order');
            $assembleia->title = $title;
            $assembleia->content = $content;
            $assembleia->status = $status;
            $assembleia->order = $order;
            $assembleia->save();

        }     

        return $array;
    }

    public function delete($id)
    {
        $array = ['error' => ''];
        $item = Assembleia::find($id);
        if ($item) {       
            // Converta a URL em um caminho relativo ao sistema de arquivos
            $relativePath = str_replace(asset(''), '', $item->thumb);
            $relativePath = str_replace('storage', '', $relativePath);
            // Use o caminho relativo para excluir o arquivo
            //var_dump($relativePath);die;
            //   Storage::delete('public/image/areas/G4RCjcZN9gMoDxvZ7BsSPkV9Egl1smtyKrNO2tVe.png');
           Storage::delete('public' . $relativePath);
           Assembleia::find($id)->delete();
        } else {
            $array['error'] = 'Documento inexistente';
            // return $array;
        }
        return $array;
    }
}
