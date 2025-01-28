<?php

namespace App\Http\Controllers;


use App\Models\Associados;
use App\Models\Setting;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\Midia;
use App\Models\Boletos;
use Illuminate\Support\Str;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Validation\ValidationException; // Adicionado



use Exception;
use Illuminate\Http\Request;


class MailController extends Controller
{

    public function enviarEmail(Request $request)
    {
        try {
            // Carregar configurações do sistema
            $settings = Setting::first();

            $companySettings = json_decode($settings->company_settings ?? '{}');
            $appearanceSettings = json_decode($settings->appearance_settings ?? '{}');


            // Validar cada email individualmente
            $emailValidator = function ($emails) {
                if (empty($emails)) return true;
                foreach (explode(',', $emails) as $email) {
                    if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL)) {
                        throw new \Exception("Email inválido: " . trim($email));
                    }
                }
                return true;
            };

            // Validações
            try {
                if (!$emailValidator($request->to)) {
                    throw new \Exception('Email(s) do destinatário inválido(s)');
                }

                if (!empty($request->cc) && !$emailValidator($request->cc)) {
                    throw new \Exception('Email(s) do CC inválido(s)');
                }

                if (!empty($request->bcc) && !$emailValidator($request->bcc)) {
                    throw new \Exception('Email(s) do CCO inválido(s)');
                }

                if (empty($request->subject)) {
                    throw new \Exception('O assunto é obrigatório');
                }

                if (empty($request->content)) {
                    throw new \Exception('O conteúdo do email é obrigatório');
                }
            } catch (\Exception $e) {
                return response()->json([
                    'error' => true,
                    'message' => $e->getMessage()
                ], 422);
            }

            // Verificar tamanho total dos anexos
            if ($request->hasFile('attachments')) {
                $totalSize = 0;
                foreach ($request->file('attachments') as $file) {
                    $totalSize += $file->getSize();

                    // Verificar se o arquivo é maior que 10MB
                    if ($file->getSize() > 10 * 1024 * 1024) {
                        throw new \Exception('O arquivo ' . $file->getClientOriginalName() . ' excede o limite de 10MB');
                    }
                }

                // Verificar se o total excede 25MB
                if ($totalSize > 25 * 1024 * 1024) {
                    throw new \Exception('O tamanho total dos anexos excede o limite de 25MB');
                }
            }

            // Preparar dados para o template
            $emailData = [
                'content' => $request->content,
                'company_name' => $companySettings->company_name ?? 'Empresa',
                'logo_url' => asset($appearanceSettings->customLogo) ?? asset('images/logo.png'),
                'primary_color' => $appearanceSettings->primaryColor ?? '#003366',
                'show_contact_info' => true,
                'phone' => $companySettings->phone ?? '',
                'email' => $companySettings->email ?? '',
                'client_name' => $request->client_name ?? null,
                'footer_message' => 'Este é um email automático. Por favor, não responda.',
                'company_slogan' => $companySettings->company_slogan ?? 'Slogan da empresa',
                'cnpj' => $companySettings->cnpj ?? '',
                'address' => $companySettings->address ?? ''


            ];

           // echo json_encode( $companySettings);die;


            //  $view = view('emails.template', $emailData)->render();
            // ou
            // \Log::info($view);die;
            // dd($view);

            // Enviar email
            Mail::send('emails.template', $emailData, function ($message) use ($request, $companySettings) {
                try {
                    // Destinatários principais
                    $toEmails = array_map('trim', explode(',', $request->to));
                    $message->to($toEmails);

                    // CC
                    if (!empty($request->cc)) {
                        $ccEmails = array_map('trim', explode(',', $request->cc));
                        $message->cc($ccEmails);
                    }

                    // BCC
                    if (!empty($request->bcc)) {
                        $bccEmails = array_map('trim', explode(',', $request->bcc));
                        $message->bcc($bccEmails);
                    }

                    // Configurar remetente com dados da empresa
                    $message->from(
                        $companySettings->mail ?? config('mail.from.address'),
                        $companySettings->company_name ?? config('mail.from.name')
                    );

                    // Assunto
                    $message->subject($request->subject);

                    // Anexar arquivos
                    if ($request->hasFile('attachments')) {
                        // Converte para array se for um único arquivo
                        $files = is_array($request->file('attachments'))
                            ? $request->file('attachments')
                            : [$request->file('attachments')];

                        foreach ($files as $file) {
                            $message->attach($file->getRealPath(), [
                                'as' => $file->getClientOriginalName(),
                                'mime' => $file->getMimeType()
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Erro ao configurar mensagem: ' . $e->getMessage());
                    throw new \Exception('Erro ao configurar mensagem do email');
                }
            });

            // Log de sucesso
            \Log::info('Email enviado com sucesso para: ' . $request->to);

            return response()->json([
                'error' => false,
                'message' => 'Email enviado com sucesso'
            ]);
        } catch (\Swift_TransportException $e) {
            // Erro específico de conexão SMTP
            \Log::error('Erro de conexão SMTP: ' . $e);
            return response()->json([
                'error' => true,
                'message' => 'Erro de conexão com o servidor de email'
            ], 500);
        } catch (\Exception $e) {
            // Outros erros
            \Log::error('Erro ao enviar email: ' . $e);
            return response()->json([
                'error' => true,
                'message' => 'Erro ao enviar email: ' . $e->getMessage()
            ], 500);
        }
    }
}
