<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

use Exception;

class UserController extends Controller
{
    public function getAll()
    {
        $array = ['error' => ''];
        $users = User::leftJoin('profiles', 'users.profile', '=', 'profiles.id')
            ->select('users.*', 'profiles.roles as profile_roles', 'profiles.name as profile_name')
            ->get();
        $array['list'] = $users;
        return $array;
    }




    public function getById($id)
    {
        try {
            $user = User::leftJoin('profiles', 'users.profile', '=', 'profiles.id')
                ->where('users.id', $id)
                ->select('users.*', 'profiles.roles as profile_roles', 'profiles.name as profile_name')
                ->first();

            if ($user) {
                $results = [
                    'error' => '',
                    'success' => true,
                    'list' => $user,
                    'message' => 'Usuário encontrado com sucesso.',
                ];
                return response()->json($results, 200);
            } else {
                $results = [
                    'error' => 'Nenhum Usuário encontrado com esse ID.',
                    'success' => false,
                    'data' => null,
                    'message' => 'Nenhum Usuário encontrado com esse ID.',
                ];
                return response()->json($results, 404);
            }
        } catch (\Exception $e) {
            $results = [
                'success' => false,
                'list' => null,
                'error' => 'Erro inesperado: ' . $e->getMessage(),
            ];
            return response()->json($results, 500);
        }
    }


    public function getByCpf($cpf)
    {
        $cpf = $cpf;

        // Verifique se o CPF foi fornecido
        if (!$cpf) {
            return response()->json([
                'error' => 'CPF não fornecido',
                'success' => false,
            ], 400);
        }

        // Busque o usuário pelo CPF usando LIKE
        $users = User::where('cpf', 'like', "%$cpf%")->get();

        if ($users->isNotEmpty()) {
            return response()->json([
                'error' => null,
                'success' => true,
                'message' => 'Usuários encontrados com sucesso',
                'list' => $users->toArray(),
                // Outros dados de resultado aqui...
            ], 200);
        } else {
            return response()->json([
                'error' => 'Nenhum usuário encontrado com esse CPF',
                'success' => false,
                'message' => 'Nenhum usuário encontrado',
                'data' => [],
                // Outros dados de resultado aqui...
            ], 404);
        }
    }



    public function update($id, Request $request)
    {

        $array = ['error' => ''];

        if ($request->input('password')) {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required|email|',
                'cpf' => 'digits:11',
                'password' => 'required',
                // 'password_confirm' => 'required|same:password',
            ]);
            if (!$validator->fails()) {
                $name = $request->input('name');
                $email = $request->input('email');
                $phone = $request->input('phone');
                $cpf = $request->input('cpf');
                $password = $request->input('password');
                $profile = $request->input('profile');

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
                    $newUser->profile = $profile;
                    $newUser->password = $hash;
                    $newUser->save();
                }
            } else {
                $array['error'] = $validator->errors()->first();
                return $array;
            }
            return $array;
        } else {

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
                $profile = $request->input('profile');


                if ($cpfUser &&  $cpfUser->cpf != $newUser->cpf) {
                    $array['error'] = 'CPF já utilizado por outro usuário! ';
                } elseif ($emailUser &&  $emailUser->email != $newUser->email) {
                    $array['error'] = 'EMAIL já utilizado por outro usuário!';
                } else {
                    $newUser->name = $name;
                    $newUser->email = $email;
                    $newUser->phone = $phone;
                    $newUser->cpf = $cpf;
                    $newUser->profile = $profile;

                    $newUser->save();
                }
            } else {
                $array['error'] = $validator->errors()->first();
                return $array;
            }
        }
        return $array;
    }

    public function updateByUser($id, Request $request)
    {
        $array = ['error' => ''];
    
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'cpf' => 'required|digits:11',
            'current_password' => [
                'required_with:new_password',
                function ($attribute, $value, $fail) use ($id) {
                    $user = User::find($id);
        
                    if (!Hash::check($value, $user->password)) {
                        $fail('Senha atual incorreta');
                    }
                },
            ],
            'new_password' => 'sometimes|min:6|different:current_password',
            'password_confirmation' => 'sometimes|required_with:new_password|same:new_password',
            'thumb' => 'sometimes|mimes:jpg,png,jpeg',
        ]);
        
    
        if ($validator->fails()) {
            $array['error'] = $validator->errors()->first();
            return $array;
        }
    
        $newUser = User::find($id);
    
        $newUser->name = $request->input('name');
        $newUser->email = $request->input('email');
        $newUser->address = $request->input('address');
        $newUser->phone = $request->input('phone');    
        // Atualizar a senha apenas se uma nova senha foi fornecida
        $newPassword = $request->input('new_password');
        if ($newPassword) {
            $hash = password_hash($newPassword, PASSWORD_DEFAULT);
            $newUser->password = $hash;
        }
    
        // Atualizar a imagem apenas se um novo arquivo for fornecido
        if ($request->file('thumb')) {
                // Salvar o arquivo no armazenamento
                $arquivo = $request->file('thumb')->store('public/users/thumb/' . $id);
                $url = asset(Storage::url($arquivo));
                $thumb_delete = $newUser->thumb_file;
                Storage::delete($thumb_delete);
                $newUser->thumb_file = $arquivo;
                $newUser->thumb = $url;
        }
    
        $newUser->save();
    
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
            $profile = $request->input('profile');

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
            $newUser->profile = $profile;
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
