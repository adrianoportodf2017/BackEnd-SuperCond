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


    public function getById($id)
    {
        $users = User::where('id', $id)->first();
        if ($users) {
            $results = [
                'error' => '',
                'list' => $users,
                // Outros dados de resultado aqui...
            ];
        } else {
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

    public function getByCpf(Request $request)
    {
        
        
        $cpf = $request->input('cpf');
        return $cpf;die;
        $users = User::where('cpf', $cpf)->first();
        if ($users) {
            $results = [
                'error' => '',
                'list' => $users,
                // Outros dados de resultado aqui...
            ];
        } else {
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

        $array = ['error' => ''];

        if ($request->input('password')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|',
                'cpf' => 'required|digits:11',
                'password' => 'required',
               // 'password_confirm' => 'required|same:password',
            ]);
            if (!$validator->fails()) {
                $name = $request->input('name');
                $email = $request->input('email');
                $phone = $request->input('phone');
                $cpf = $request->input('cpf');
                $password = $request->input('password');
                $hash = password_hash($password, PASSWORD_DEFAULT);                  
                    $newUser = User::find($id);
                    $cpfUser = User::where('cpf', $cpf)->first();
                    $emailUser = User::where('email', $email)->get()->first();
    
                    if ($cpfUser &&  $cpfUser->cpf != $newUser->cpf) {
                        $array['error'] = 'CPF já utilizado por outro usuário! ';
                    } elseif ($emailUser &&  $emailUser->email != $newUser->email) {
                        $array['error'] = 'EMAIL já utilizado por outro usuário!';
                    } else {
                        $newUser->name = $name;
                        $newUser->email = $email;
                        $newUser->phone = $phone;
                        $newUser->cpf = $cpf;
                        $newUser->password = $hash;
                        $newUser->save();
                    }
                } else {
                    $array['error'] = $validator->errors()->first();
                    return $array;
                }  return $array;
            }
         else {

            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email',
                'cpf' => 'required|digits:11',
                //'password' => 'required',
                // 'password_confirm' => 'required|same:password',
            ]);
            if (!$validator->fails()) {
                $name = $request->input('name');
                $email = $request->input('email');
                $phone = $request->input('phone');
                $cpf = $request->input('cpf');
                $newUser = User::find($id);
                $cpfUser = User::where('cpf', $cpf)->first();
                $emailUser = User::where('email', $email)->get()->first();

                if ($cpfUser &&  $cpfUser->cpf != $newUser->cpf) {
                    $array['error'] = 'CPF já utilizado por outro usuário! ';
                } elseif ($emailUser &&  $emailUser->email != $newUser->email) {
                    $array['error'] = 'EMAIL já utilizado por outro usuário!';
                } else {
                    $newUser->name = $name;
                    $newUser->email = $email;
                    $newUser->phone = $phone;
                    $newUser->cpf = $cpf;
                    $newUser->save();
                }
            } else {
                $array['error'] = $validator->errors()->first();
                return $array;
            }
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
            $phone = $request->input('phone');
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
            $newUser->phone = $phone;

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
        if ($item) {
            User::find($id)->delete();
        } else {
            $array['error'] = 'Aviso inexistente';
            // return $array;
        }
        return $array;
    }
}
