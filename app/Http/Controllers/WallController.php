<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Wall;
use App\Models\WallLike;
use Illuminate\Support\Facades\Validator;

class WallController extends Controller
{
    public function getAll() {
        $array = ['error' => '', 'list' => []];
        $user = auth()->user();
        $walls = Wall::all();
        foreach($walls as $wallKey => $wallValue) {
            $walls[$wallKey]['likes'] = 0;
            $walls[$wallKey]['liked'] = false;
            $likes = WallLike::where('id_wall', $wallValue['id'])->count();
            $walls[$wallKey]['likes'] = $likes;
            $meLikes = WallLike::where('id_wall', $wallValue['id'])
                ->where('id_user', $user['id'])
                ->count();
            if($meLikes > 0) {
                $walls[$wallKey]['liked'] = true;
            }
        }
        $array['list'] = $walls;
        return $array;
    }
    public function like($id) {
        $array = ['error' => ''];
        $user = auth()->user();
        $meLikes = WallLike::where('id_wall',$id)
            ->where('id_user', $user['id'])
            ->count();
        if($meLikes > 0) {
            WallLike::where('id_wall',$id)
            ->where('id_user', $user['id'])
            ->delete();
            $array['liked'] = false;
        } else {
            $newLike = new WallLike();
            $newLike-> id_wall = $id;
            $newLike->id_user = $user['id'];
            $newLike->save();
            $array['liked'] = true;
        }
        $array['likes'] = WallLike::where('id_wall', $id)->count();
        return $array;
    }

    public function update($id, Request $request)
    {
        
        $array['id'] =  $id;
        $title = $request->input('title');
        $body = $request->input('body');          
        $item = Wall::find($id);
            if ($item) {
                $item->title = $title;
                $item->body = $body;
                $array['error'] = '';
                $item->save();
                return $array;            
            } else {
                $array['error'] = 'Erro Ao salvar';
                return $array;
            }
        return $array;
    }
    public function insert(Request $request)
    {        
         $array = ['error' => ''];
         $validator = Validator::make($request->all(), [
            'title' => 'required',
            'body' => 'required'
        ]);
        if (! $validator->fails()) {
            $title = $request->input('title');
            $body = $request->input('body');
            $newWall = new Wall();
            $newWall->title = $title;
            $newWall->body = $body;
            $newWall->datecreated = date('Y-m-d H:m:s');
            $newWall->save();
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }
        return $array;
}

public function delete($id)
{        
     $array = ['error' => ''];
     $item = Wall::find($id);
     if($item){
        Wall::find($id)->delete();
     }
     else {
        $array['error'] = 'Aviso inexistente';
       // return $array;
    }
    return $array;
}
}