<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function sendEmailContact(Request $request)
    {
      

        $validator = Validator::make($request->all(), [
            'con_name' => 'required|string|max:255',
            'con_email' => 'required|email|max:255',
            'con_message' => 'required|string',
        ]);

        // Retornar uma mensagem de erro se a validação falhar
        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()->first(),
                'code' => 422,
            ], 422);
        }

        $data = [
            'name' => $request->input('con_name'),
            'email' => $request->input('con_email'),
            'message' => $request->input('con_message'),
        ];

        try {
            Mail::send('emails.contact', ['data' => $data], function($message) use ($data) {
                $message->to('contato@devcondbackend.agenciatecnet.com.br') // Coloque aqui o e-mail para onde será enviado
                        ->subject('Nova Mensagem de Contato');
                $message->from($data['email'], $data['name']);
            });

            return response()->json(['success' => true, 'message' => 'Email sent successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => ['error' => $e->getMessage()]]);
        }
    }

    public function testEmail()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'adrianobr00@gmail.com', // Substitua pelo email que deseja usar no campo "From"
        ];

        try {
            Mail::raw('This is a simple contact message', function($message) use ($data) {
                $message->to('sitesprontobr@gmail.com') // Coloque aqui o e-mail para onde será enviado
                        ->subject('Nova Mensagem de Contato');
                $message->from($data['email'], $data['name']);
            });

            Log::info('Email de contato simples enviado', ['data' => $data]);

            return response()->json(['success' => true, 'message' => 'Email sent successfully.']);
        } catch (\Exception $e) {
            Log::error('Erro ao enviar email de contato simples', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Erro ao enviar email: ' . $e->getMessage()]);
        }
    }
}
