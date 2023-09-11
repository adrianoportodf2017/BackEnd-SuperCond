<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function getAll()
    {
        $array = ['error' => ''];
        $users = User::all();
        $array['list'] = $users;
        return $array;
    }

    public function search(Request $request)
{
    $cpf = $request->input('q');
    $users = User::where('cpf', $cpf)->first();
    if($users){
        $results = [
            'error' => '',
            'list' => $users,
            // Outros dados de resultado aqui...
        ];

    }else{
        $results = [
            'error' => 'Nenhum Usuário encontrado com esse CPF',
            'list' => '',
            // Outros dados de resultado aqui...
        ];

    }

    // Realize a lógica de pesquisa com base no valor de $q
    // Por exemplo, você pode consultar o banco de dados para encontrar os resultados desejados.

    // Suponha que você deseja retornar um array como resultado de pesquisa para este exemplo:
  

    return response()->json($results);
}
   

    public function update($id, Request $request)
    {
        
        $array['id'] =  $id;
        $title = $request->input('title');
        $body = $request->input('body');          
        $item = User::find($id);
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
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|digits:11|unique:users,cpf',
            'password' => 'required',
           // 'password_confirm' => 'required|same:password',
        ]);
        if (!$validator->fails()) {
            $name = $request->input('name');
            $email = $request->input('email');
            $cpf = $request->input('cpf');
            $password = $request->input('password');
            $hash = password_hash($password, PASSWORD_DEFAULT);
            /**
             * 
             * salvar usuario no bd
             */
            $newUser = new User();
            $newUser->name = $name;
            $newUser->email = $email;
            $newUser->cpf = $cpf;
            $newUser->password = $hash;
            $newUser->save();
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }
        return $array;
}

public function delete($id)
{        
     $array = ['error' => ''];
     $item = User::find($id);
     if($item){
        User::find($id)->delete();
     }
     else {
        $array['error'] = 'Aviso inexistente';
       // return $array;
    }
    return $array;
}
}