<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Doc;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;


class DocController extends Controller
{
    public function getAll()
    {
        $array = ['error' => ''];

        $docs = Doc::all();

        foreach ($docs as $docKey => $docValue) {
            $docs[$docKey]['fileurl'] = $docValue['fileurl'];
        }

        $array['list'] = $docs;

        return $array;
    }

    public function insert(Request $request)
    {
        $array = ['error' => ''];
        //   var_dump($request);

        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            'file' => 'required|mimes:jpg,png,pdf,jpeg'
        ]);
        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        } elseif ($request->hasfile('file')) {
            if ($request->file('file')->isValid()) {
                $arquivo = $request->file('file')->store('public');
                $url = asset(Storage::url($arquivo));
                $title = $request->input('title');
                $newDoc = new Doc();
                $newDoc->title = $title;
                $newDoc->fileurl = $url;
                $newDoc->filename = $arquivo;
                $newDoc->datecreated = date('Y-m-d H:m:s');
                $newDoc->save();
            }
        } else {
            $array['error'] = 'Não foi enviando nenhum arquivo';
        }
        return $array;
    }
    public function update($id, Request $request)
    {
        $array['id'] =  $id;
        if ($request->hasfile('file')) {
            if ($request->file('file')->isValid()) {

                $validator = Validator::make($request->all(), [
                    'title' => 'required|min:2',
                    'file' => 'required|mimes:jpg,png,pdf,jpeg'
                ]);
                if ($validator->fails()) {
                    $array['error'] = $validator->errors()->first();
                    return $array;
                } else {
                    $title = $request->input('title');
                    $arquivo = $request->file('file')->store('public');
                    $url = asset(Storage::url($arquivo));
                    $item = Doc::find($id);
                    if ($item) {
                        $fileDelete = $item->filename;
                        $item->title = $title;
                        $item->fileurl = $url;
                        $item->filename = $arquivo;
                        $array['error'] = '';
                        $item->save();
                        Storage::delete($fileDelete);
                        $array['salvo'] = 'salvo com sucesso';
                        return $array;
                    } else {
                        $array['error'] = 'Erro Ao salvar';
                        return $array;
                    }
                }
            }
        } else {
            $validator = Validator::make($request->all(), [
                'title' => 'required|min:2',
            ]);
            if ($validator->fails()) {
                $array['error'] = $validator->errors()->first();
                $array['error'] = $request->input('title');
                return $array;
            } else {
                $title = $request->input('title');
                $item = Doc::find($id);
                if ($item) {
                    $fileDelete = $item->fileurl;
                    $item->title = $title;
                    $array['error'] = '';
                    $item->save();
                    return $array;
                } else {
                    $array['error'] = 'Erro Ao salvar';
                    return $array;
                }
            }
        }
        return $array;
    }

    public function delete($id)
    {
        $array = ['error' => ''];
        $item = Doc::find($id);
        if ($item) {
            $fileDelete = $item->filename;
            Storage::delete($fileDelete);
            Doc::find($id)->delete();
        } else {
            $array['error'] = 'Documento inexistente';
            // return $array;
        }
        return $array;
    }
}
