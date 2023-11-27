<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\Rules\Password as RulesPassword;
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
                ->where('owner_id', $user['id'])
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
            'password' => 'required'
        ]);

        if (!$validator->fails()) {
            $cpf = $request->input('cpf');
            $password = $request->input('password');

            $token = auth()->attempt([
                'cpf' => $cpf,
                'password' => $password
            ]);

            if (!$token) {
                $array['error'] = 'CPF e/ou senha incorretos!';
                return $array;
            }

            $array['token'] = $token;

            $user = auth()->user();
            $array['user'] = User::leftJoin('profiles', 'users.profile', '=', 'profiles.id')
            ->select('users.*', 'profiles.roles as profile_roles', 'profiles.name as profile_name')
            ->where('users.id', $user['id'])
            ->get();

            $properties = Unit::select(['units.id', 'units.name as unit_name', 'units.condominio_id', 'condominios.name as condominio_name', 'condominios.*'])
                ->leftJoin('condominios', 'condominios.id', '=', 'units.condominio_id')
                ->where('units.owner_id', $user['id'])
                ->get();

                

            $array['user']['properties'] = $properties;
        } else {
            $array['error'] = $validator->errors()->first();
        }

        return $array;
    }
    public function validateToken()
    {
        $array = ['error' => ''];

        $user = auth()->user();
        $array['user'] = $user;

        $properties = Unit::select(['units.id', 'units.name as unit_name', 'units.condominio_id', 'condominios.name as condominio_name', 'condominios.*'])
            ->leftJoin('condominios', 'condominios.id', '=', 'units.condominio_id')
            ->where('units.owner_id', $user['id'])
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
                ->where('owner_id', $user['id'])
                ->get();
            $array['users']['properties'] = $properties;*/
        } else {
            $array['error'] = $validator->errors()->first();
            return $array;
        }

        return $array;
    }


    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT) {
            return [
                'error' => false,
                'message' => __($status)
            ];
        } else {
            return
                [
                    'error' => true,
                    'message' => __($status)
                ];
        }
    }

    public function reset(Request $request)
    {
    
        $array = ['error' => '', 'message' => ''];
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', RulesPassword::defaults()],
        ]);

        if (!$validator->fails()) {
            $status = Password::reset(
                $request->only('email', 'password', 'passwordConfirm', 'token'),
                function ($user) use ($request) {
                    $user->forceFill([
                        'password' => Hash::make($request->password),
                        'remember_token' => Str::random(60),
                    ])->save();    
                    $user->tokens()->delete();    
                    event(new PasswordReset($user));
                }
            );

            if ($status == Password::PASSWORD_RESET) {
                $array['message'] = 'Senha Atualizada com sucesso!';
            }else{
                $array['message'] = 'Erro ao resetar a senha';
                $array['error'] = __($status);       
           }
        } else {
            $array['error'] = $validator->errors()->first();
        } 
        return $array;
        
    }



    /**
     * @return array
     */
    public function logout()
    {
        $array = ['error' => ''];
        auth()->logout();
        return $array;
    }
}
