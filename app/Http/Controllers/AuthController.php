<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Unit;


class AuthController extends Controller
{
    //


    public function unauthorized()
    {
        return response()->json([
            'error' => 'Não Autorizado'
        ], 401);
    }

    public function register(Request $request)
    {
        $array = ['error' => ''];
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'cpf' => 'required|digits:11|unique:users,cpf',
            'password' => 'required',
            'password_confirm' => 'required|same:password',
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
            /*
Logar usuario no sistema
**/
            $token = auth()->attempt([
                'cpf' => $cpf,
                'password' => $password
            ]);

            if (!$token) {
                $array['error'] = 'Usuário e/ou senha Inválidos';
                return $array;
            }
            $array['token'] = $token;
            $user = auth()->user();
            $array['user'] = $user;

            $properties = Unit::select(['id', 'name'])
                ->where('id_owner', $user['id'])
                ->get();
            $array['users']['properties'] = $properties;
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }



        return $array;
    }

    public function login(Request $request)
    {
        $array = ['error' => ''];
        $validator = Validator::make($request->all(), [
            'cpf' => 'required|digits:11',
            'password' => 'required',
        ]);

        if (!$validator->fails()) {
            $cpf = $request->input('cpf');
            $password = $request->input('password');

            $token = auth()->attempt([
                'cpf' => $cpf,
                'password' => $password
            ]);

            if (!$token) {
                $array['error'] = 'Usuário e/ou senha Inválidos';
                return $array;
            }
            $array['token'] = $token;
            $user = auth()->user();
            $array['user'] = $user;

            $properties = Unit::select(['id', 'name'])
                ->where('id_owner', $user['id'])
                ->get();
            $array['users']['properties'] = $properties;
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }


    public function validateToken() {
        $array = ['error' => ''];

        $user = auth()->user();
        $array['user'] = $user;

        $properties = Unit::select(['id', 'name'])
        ->where('id_owner', $user['id'])
        ->get();

        $array['user']['properties'] = $properties;

        return $array;
    }


    public function loginAdmin(Request $request)
    {
        $array = ['error' => ''];
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!$validator->fails()) {
            $email = $request->input('email');
            $password = $request->input('password');

            $token = auth()->attempt([
                'email' => $email,
                'password' => $password,
                'profile' => '1'
            ]);

            if (!$token) {
                $array['error'] = 'Usuário e/ou senha Inválidos';
                return $array;
            }
            $array['token'] = $token;
            $user = auth()->user();
            $array['user'] = $user;
            /*$properties = Unit::select(['id', 'name'])
                ->where('id_owner', $user['id'])
                ->get();
            $array['users']['properties'] = $properties;*/
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }
}
