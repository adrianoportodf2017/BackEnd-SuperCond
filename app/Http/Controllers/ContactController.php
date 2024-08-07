<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
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

        $mail = new PHPMailer(true);
        
        try {
            // Configurações do servidor
            $mail->isSMTP();                                            // Enviar usando SMTP
            $mail->Host       = 'sh-pro86.hostgator.com.br';                  // Configure o host do servidor de e-mail
            $mail->SMTPAuth   = true;                                   // Habilitar autenticação SMTP
            $mail->Username   = 'contato@agenciatecnet.com.br';              // SMTP username
            $mail->Password   = '0307199216@Dr';                             // SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Habilitar criptografia TLS
            $mail->Port       = 465;    
            
               // Habilitar debug
               $mail->SMTPDebug = 3;                                       // Nível de debug (0 = off, 1 = mensagens de cliente, 2 = mensagens de cliente e servidor, 3 = mensagens completas)
               $mail->Debugoutput = function($str, $level) {
                   Log::info("PHPMailer: [$level] $str");
               };// Porta TCP para conexão

            // Recipientes
            $mail->setFrom($data['email'], $data['name']);
            $mail->addAddress('adrianobr00@gmail.com');               // Adicione um destinatário

            // Conteúdo do e-mail
            $mail->isHTML(true);                                        // Defina o formato do email para HTML
            $mail->Subject = 'Nova Mensagem de Contato';
            $mail->Body    = $data['message'];
            $mail->AltBody = strip_tags($data['message']);              // Texto alternativo para clientes de email sem suporte a HTML

            $mail->send();

            return response()->json(['success' => true, 'message' => 'Email sent successfully.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao enviar email: ' . $mail->ErrorInfo]);
        }
    }
}
