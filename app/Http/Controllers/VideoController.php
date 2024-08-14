<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Midia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Exception;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        $videos = Video::all();
        $data = [];

        if (!$videos) {
            return response()->json([
                'error' => 'Nenhum vídeo encontrado',
                'code' => 404,
            ], 404);
        }

        foreach ($videos as $video) {
            $midias = $video->midias;
            $data[] = $video;
        }

        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $data,
        ], 200);
    }

    public function getAllPrivate()
    {
        $videos = Video::where('status', 1)
        ->where('restricted_area', 'true')
        ->orderBy('date_event')
        ->get();

        if (!$videos) {
            return response()->json([
                'error' => 'Nenhum vídeo encontrado',
                'code' => 404,
            ], 404);
        }

        $data = [];
        foreach ($videos as $video) {
            $midias = $video->midias;
            $data[] = $video;
        }

        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $data,
        ], 200);
    }

    public function getById($id)
    {
        $video = Video::where('id', $id)->first();

        if (!$video) {
            return response()->json([
                'error' => "Vídeo não encontrado",
                'code' => 404,
            ], 404);
        }

        $midias = $video->midias;
        $video['midias'] = $midias;

        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $video,
        ], 200);
    }

    public function insert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
            //'thumb' => 'mimes:jpg,png,jpeg',
            //'media_file' => 'required|mimes:mp4|max:50000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }

        $newVideo = new Video();

        if ($request->file('thumb') && !$request->file('thumb')->isValid()) {
            return response()->json([
                'error' => 'O arquivo enviado não é válido',
                'code' => 400,
            ], 400);
        }

        if ($request->hasfile('thumb')) {
            $arquivo = $request->file('thumb')->store('public/videos/thumb');
            $thumbUrl = asset(Storage::url($arquivo));
        } else {
            $arquivo = '';
            $thumbUrl = '';
        }

        if ($request->hasfile('media_file')) {
            $mediaFile = $request->file('media_file')->store('public/videos/media');
            $mediaUrl = asset(Storage::url($mediaFile));
        } else {
            $mediaFile = '';
            $mediaUrl = '';
        }

        $newVideo->title = $request->input('title');
        $newVideo->content = $request->input('content');
        $newVideo->thumb = $thumbUrl;
        $newVideo->thumb_file = $arquivo;
        $newVideo->url =  $request->input('url');
        $newVideo->status = $request->input('status');
        $newVideo->date_event = $request->input('date_event');
        $newVideo->video_duration = $request->input('video_duration');        
        $newVideo->likes = $request->input('likes', '0');
        $newVideo->public_area = $request->input('public_area', '0');
        $newVideo->restricted_area = $request->input('restricted_area', '0');
        $newVideo->slug = $request->input('slug');
        $newVideo->tags = $request->input('tags');

        try {
            $newVideo->save();
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao salvar o vídeo!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $newVideo,
        ], 201);
    }

    public function update($id, Request $request)
    {
        $video = Video::find($id);

        if (!$video) {
            return response()->json([
                'error' => 'Vídeo inexistente',
                'code' => 404,
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'code' => 400,
            ], 400);
        }

        if ($request->file('thumb')) {
            $validator = Validator::make($request->all(), [
                'thumb' => 'mimes:jpg,png,jpeg',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'error' => 'O arquivo enviado não é válido',
                    'code' => 400,
                ], 400);
            }

            $arquivo = $request->file('thumb')->store('public/videos/thumb');
            $thumbUrl = asset(Storage::url($arquivo));
            Storage::delete($video->thumb_file);
            $video->thumb_file = $arquivo;
            $video->thumb = $thumbUrl;
        }

        $video->title = $request->input('title');
        $video->content = $request->input('content');
        $video->status = $request->input('status');
        $video->url =  $request->input('url');
        $video->date_event = $request->input('date_event');
        $video->video_duration = $request->input('video_duration');   
        $video->likes = $request->input('likes', 0);
        $video->public_area = $request->input('public_area', '0');
        $video->restricted_area = $request->input('restricted_area', '0');
        $video->slug = $request->input('slug');
        $video->tags = $request->input('tags');

        try {
            $video->save();
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao salvar o vídeo!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $video,
        ], 200);
    }

    public function insertMidia($id, Request $request)
    {
        $video = Video::find($id);

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:mp4|max:50000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'code' => 400,
            ], 400);
        }

        if (!$video) {
            return response()->json([
                'error' => 'Vídeo inexistente',
                'code' => 404,
            ], 404);
        }

        if (!$request->hasfile('file')) {
            return response()->json([
                'error' => 'Nenhum arquivo enviado',
                'code' => 400,
            ], 400);
        }

        if (!$request->file('file')->isValid()) {
            return response()->json([
                'error' => 'O arquivo enviado não é válido',
                'code' => 400,
            ], 400);
        }

        $mediaFile = $request->file('file')->store('public/videos/' . $id);
        $mediaUrl = asset(Storage::url($mediaFile));

        $midia = new Midia([
            'title' => $video->title,
            'url' => $mediaUrl,
            'file' => $mediaFile,
            'status' => 'ativo',
            'type' => 'video',
            'user_id' => $request->input('user_id')
        ]);

        try {
            $video->midias()->save($midia);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao salvar novo item!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        return response()->json([
            'error' => '',
            'success' => true,
            'list' => $video,
        ], 200);
    }

    public function deleteMidia($id)
    {
        $midia = Midia::find($id);

        if (!$midia) {
            return response()->json([
                'error' => 'Mídia inexistente',
                'code' => 404,
            ], 404);
        }

        try {
            $midia->delete();
            Storage::delete($midia->file);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Erro ao deletar mídia!',
                'detail' => $e->getMessage(),
                'code' => 500,
            ], 500);
        }

        return response()->json([
            'error' => '',
            'success' => true,
        ], 200);
    }

    public function delete($id)
    {
        $video = Video::find($id);
        if (!$video) {
            return response()->json([
                'error' => 'Vídeo inexistente',
                'code' => 404,
            ], 404);
        }
        Storage::delete($video->thumb_file);
        $video->delete();
        return response()->json([
            'error' => '',
            'success' => true,
        ], 200);
    }
}
