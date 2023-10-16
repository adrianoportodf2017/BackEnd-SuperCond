<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentosAssembleia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Exception;


class DocumentosAssembleiaController extends Controller
{
    public function getAll()
    {
        $array = ['error' => ''];
        $docs = DocumentosAssembleia::all();
             // Verifica se existem reservas
             if (!$docs) {
                return response()->json([
                    'error' => 'Nenhuma reserva encontrada',
                    'list' => [],
                    'code' => 404,
                ], 404);
            }    
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
    {        //return var_dump($_FILES);exit;


        $array = ['error' => ''];
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            'file' => 'mimes:jpg,png,jpeg,pdf',
            'thumb' => 'mimes:jpg,png,jpeg'

            //'year' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' =>  $validator->errors()->first(),
                'code' => 404,
            ], 404)    ;       
        } else {


            if ($request->hasfile('file')) {
                if ($request->file('file')->isValid()) {
                    $arquivo_documents = $request->file('file')->store('public/upload/documentos_assembleias/documentos');
                    $url_documents = asset(Storage::url($arquivo_documents));
                }
            } else {
                $url_documents = null;
            }
            if ($request->hasfile('thumb')) {

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

        // Retornar uma resposta de sucesso com os dados da unidade
        return response()->json([
            'error' => '',
            'success' => true,
            'documento' => $newAssembleia,
        ], 200);
    }


    public function update($id, Request $request)
    {
        $array = ['error' => ''];
        // var_dump($request->input());die;
        $array['id'] =  $id;
        $doc = DocumentosAssembleia::find($id);
        if (!$doc) {
            return response()->json([
                'error' => 'Nenhum documento  encontrada',
                'code' => 404,
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            'file' => 'mimes:jpg,png,jpeg,pdf',
            'thumb' => 'mimes:jpg,png,jpeg'
            //'year' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'error' =>  $validator->errors()->first(),
                'code' => 404,
            ], 404);
        } else {
            if ($request->hasfile('file')) {

                if ($request->file('file')->isValid()) {
                    $fileDelete = $doc->file_url;
                    $arquivo_documents = $request->file('file')->store('public/upload/documentos_assembleias/documentos');
                    $url_documents = asset(Storage::url($arquivo_documents));
                    // Converta a URL em um caminho relativo ao sistema de arquivos
                    $relativePath = str_replace(asset(''), '', $fileDelete);
                    $relativePath = str_replace('storage', '', $relativePath);
                    // Use o caminho relativo para excluir o arquivo        
                    Storage::delete('public' . $relativePath);
                } else {
                    return response()->json([
                        'error' =>  $validator->errors()->first(),
                        'code' => 404,
                    ], 404);
                }
            } else {
                $url_documents =  $doc->file_url;
            }
            if ($request->hasfile('thumb')) {

                if ($request->file('thumb')->isValid()) {
                    $fileDeletethumb = $doc->thumb;

                    $thumb = $request->file('thumb')->store('public/upload/documentos_assembleias/thumbs');
                    $url = asset(Storage::url($thumb));
                    $relativePathThumb = str_replace(asset(''), '', $fileDeletethumb);
                    $relativePathThumb = str_replace('storage', '', $relativePathThumb);
                    // Use o caminho relativo para excluir o arquivo        
                    Storage::delete('public' . $relativePathThumb);
                } else {
                    return response()->json([
                        'error' =>  $validator->errors()->first(),
                        'code' => 404,
                    ], 404);
                }
            } else {
                $url =  $doc->thumb;
            }

            $title = $request->input('title');
            $content = $request->input('content');
            $status = $request->input('status');
            $doc->title = $title;
            $doc->content = $content;
            $doc->status = $status;
            $doc->file_url = $url_documents;
            $doc->thumb = $url;
            // Salvar o documento no banco de dados
            try {
                $doc->save();
            } catch (Exception $e) {
                // Tratar o erro
                return response()->json([
                    'error' => 'Erro ao salvar Documento!',
                    'detail' => $e->getMessage(),
                    'code' => 500,
                ], 500);
            }
        }
        // Retornar uma resposta de sucesso com os dados da unidade
        return response()->json([
            'error' => '',
            'success' => true,
            'documento' => $doc,
        ], 200);
    }


    public function delete($id)
    {
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

                  // Salvar o documento no banco de dados
                  try {
                    $item->delete();
                } catch (Exception $e) {
                    // Tratar o erro
                    return response()->json([
                        'error' => 'Erro ao Deletar Documento!',
                        'detail' => $e->getMessage(),
                        'code' => 500,
                    ], 500);
                }
        } else {
            // Tratar o erro
            return response()->json([
                'error' => 'Documento inexistente!',
                'code' => 500,
            ], 500);
            // return $array;
        }
        return response()->json([
            'error' => '',
            'success' => 'Documento deletado com sucesso',            
        ], 201);
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
