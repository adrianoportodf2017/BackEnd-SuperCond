<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentosAssembleia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class DocumentosAssembleiaController extends Controller
{
    public function getAll()
    {
        $array = ['error' => ''];
        $docs = DocumentosAssembleia::all();
        $array['list'] = $docs;
        return $array;
    }

    public function getDocumentsAssembleia($id)
    {
        $array = ['error' => ''];
        $docs = DocumentosAssembleia::where('assembleia_id', $id)->get();
        $array['list'] = $docs;
        return $array;
    }

    public function insert(Request $request)
    {
        $array = ['error' => ''];
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            //'year' => 'required',
        ]);
        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        } else {
            if ($request->hasfile('file')) {

                $validator = Validator::make($request->all(), [
                    'file' => 'required|mimes:jpg,png,jpeg,pdf'
                ]);
                if ($request->file('file')->isValid()) {
                    $arquivo_documents = $request->file('file')->store('public/image/assembleias/documents');
                    $url_documents = asset(Storage::url($arquivo_documents));
                }
            } else {
                $url_documents = null;
            }
            if ($request->hasfile('thumb')) {

                $validator = Validator::make($request->all(), [
                    'thumb' => 'required|mimes:jpg,png,jpeg'
                ]);

                if ($request->file('thumb')->isValid()) {
                    $arquivo = $request->file('thumb')->store('public/image/assembleias');
                    $url = asset(Storage::url($arquivo));
                }
            } else {
                $url = null;
            }
            $title = $request->input('title');
            $content = $request->input('content');
            $status = $request->input('status');
            $assembleia_id = $request->input('assembleia_id');
            $newAssembleia = new DocumentosAssembleia();
            $newAssembleia->title = $title;
            $newAssembleia->content = $content;
            $newAssembleia->status = $status;
            $newAssembleia->assembleia_id = $assembleia_id;
            $newAssembleia->file_url = $url_documents;
            $newAssembleia->thumb = $url;
            $newAssembleia->created_at = date('Y-m-d H:m:s');
            $newAssembleia->save();
        }
        return $array;
    }
    public function update($id, Request $request)
    {
        $array = ['error' => ''];
        // var_dump($request->input());die;
        $array['id'] =  $id;
        $assembleia = DocumentosAssembleia::find($id);
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
            $year = $request->input('year');

            $assembleia->title = $title;
            $assembleia->content = $content;
            $assembleia->status = $status;
            $assembleia->order = $order;
            $assembleia->year = $year;
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
            $year = $request->input('year');

            $assembleia->title = $title;
            $assembleia->content = $content;
            $assembleia->status = $status;
            $assembleia->order = $order;
            $assembleia->year = $year;

            $assembleia->save();
        }

        return $array;
    }

    public function delete($id)
    {
        $array = ['error' => ''];
        $item = DocumentosAssembleia::find($id);
        if ($item) {
            // Converta a URL em um caminho relativo ao sistema de arquivos
            $relativePath = str_replace(asset(''), '', $item->thumb);
            $relativePath = str_replace('storage', '', $relativePath);
            // Use o caminho relativo para excluir o arquivo
            //var_dump($relativePath);die;
            //   Storage::delete('public/image/areas/G4RCjcZN9gMoDxvZ7BsSPkV9Egl1smtyKrNO2tVe.png');
            Storage::delete('public' . $relativePath);
            DocumentosAssembleia::find($id)->delete();
        } else {
            $array['error'] = 'Documento inexistente';
            // return $array;
        }
        return $array;
    }

    public function updateStatus($id, Request $request)
    {
        $array = ['error' => ''];
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);
        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        } else {
            $item = DocumentosAssembleia::find($id);
            $item->status = $request->input('status');
            $item->save();
            return $request->input();
        }
    }
}
