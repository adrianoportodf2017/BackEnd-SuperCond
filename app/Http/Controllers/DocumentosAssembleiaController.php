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

    public function getDocumentosAssembleia($id)
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
                    $arquivo_documents = $request->file('file')->store('public/upload/documentos_assembleias/documentos');
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
                    $arquivo = $request->file('thumb')->store('public/upload/documentos_assembleias/thumbs');
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
        $doc = DocumentosAssembleia::find($id);
        if (!$doc) {
            $array['error'] = 'Registro não encontrado';
            return $array;
            exit();
        }
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
                    $fileDelete = $doc->file_url;

                    $arquivo_documents = $request->file('file')->store('public/upload/documentos_assembleias/documents');
                    $url_documents = asset(Storage::url($arquivo_documents));
                    // Converta a URL em um caminho relativo ao sistema de arquivos
                    $relativePath = str_replace(asset(''), '', $fileDelete);
                    $relativePath = str_replace('storage', '', $relativePath);
                    // Use o caminho relativo para excluir o arquivo        
                    Storage::delete('public' . $relativePath);
                } else {
                    $array['error'] = $validator->errors()->first();
                    return $array;
                }
            } else {
                $url_documents = null;
            }
            if ($request->hasfile('thumb')) {

                $validator = Validator::make($request->all(), [
                    'thumb' => 'required|mimes:jpg,png,jpeg'
                ]);

                if ($request->file('thumb')->isValid()) {
                    $fileDeletethumb = $doc->thumb;

                    $thumb = $request->file('thumb')->store('public/upload/documentos_assembleias/thumbs');
                    $url = asset(Storage::url($thumb));
                    $relativePathThumb = str_replace(asset(''), '', $fileDeletethumb);
                    $relativePathThumb = str_replace('storage', '', $relativePathThumb);
                    // Use o caminho relativo para excluir o arquivo        
                    Storage::delete('public' . $relativePathThumb);
                } else {
                    $array['error'] = $validator->errors()->first();
                    return $array;
                }
            } else {
                $url = null;
            }

            $title = $request->input('title');
            $content = $request->input('content');
            $status = $request->input('status');
            $doc->title = $title;
            $doc->content = $content;
            $doc->status = $status;
            $doc->file_url = $url_documents;
            $doc->thumb = $url;
            $doc->save();
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
            $relativePath = str_replace(asset(''), '', $item->file_url);
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
