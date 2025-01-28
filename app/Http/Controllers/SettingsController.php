<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;


class SettingsController extends Controller
{
   public function getCompanySettings()
   {
    Cache::forget('settings');

    $settings = Cache::remember('settings', 60 * 24, function () {
        return Setting::first();
    });       
       if (!$settings) {
           return response()->json([
               'error' => '',
               'settings' => [
                   'company_name' => '',
                   'company_slogan' => '',
                   'cnpj' => '',
                   'phone' => '',
                   'email' => '',
                   'address' => '',
                   'city' => '',
                   'state' => '',
                   'zip_code' => '',
                   'website' => ''
               ]
           ]);
       }

       return response()->json([
           'error' => '',
           'settings' => json_decode($settings->company_settings)
       ]);
   }

   public function updateCompanySettings(Request $request)
   {
       $validator = Validator::make($request->all(), [
           'company_name' => 'required|string',
           'cnpj' => 'required|string',
           'phone' => 'required|string',
           'email' => 'required|email',
           'address' => 'required|string',
           'city' => 'required|string',
           'state' => 'required|string',
           'zip_code' => 'required|string'
       ]);

       if ($validator->fails()) {
           return response()->json(['error' => $validator->errors()->first()]);
       }

       try {
           $settings = Setting::firstOrNew();
           $settings->company_settings = json_encode($request->all());
           $settings->save();
           Cache::forget('settings');
           return response()->json([
               'error' => '',
               'settings' => $request->all()
           ]);
       } catch (\Exception $e) {
           return response()->json(['error' => 'Erro ao salvar configurações: ' . $e->getMessage()]);
       }
   }

   public function getEmailSettings()
   {
       $settings = Setting::first();
       
       if (!$settings || !$settings->email_settings) {
           return response()->json([
               'error' => '',
               'settings' => [
                   'smtp_host' => '',
                   'smtp_port' => '',
                   'smtp_user' => '',
                   'smtp_password' => '',
                   'smtp_encryption' => 'tls',
                   'from_name' => '',
                   'from_email' => '',
                   'reply_to' => ''
               ]
           ]);
       }

       return response()->json([
           'error' => '',
           'settings' => json_decode($settings->email_settings)
       ]);
   }

    public function updateEmailSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'smtp_host' => 'required|string',
            'smtp_port' => 'required|integer',
            'smtp_user' => 'required|string',
            'smtp_password' => 'required|string',
            'smtp_encryption' => 'required|in:tls,ssl,none',
            'from_name' => 'required|string',
            'from_email' => 'required|email',
            'reply_to' => 'nullable|email'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()]);
        }

        try {
            $settings = Setting::firstOrNew();
            $settings->email_settings = json_encode($request->all());
            $settings->save();

            // Limpa o cache das configurações
            Cache::forget('settings');

            // Atualiza as configurações em tempo real
            $emailSettings = (object)$request->all();
            config([
                'mail.mailers.smtp.host' => $emailSettings->smtp_host,
                'mail.mailers.smtp.port' => $emailSettings->smtp_port,
                'mail.mailers.smtp.username' => $emailSettings->smtp_user,
                'mail.mailers.smtp.password' => $emailSettings->smtp_password,
                'mail.mailers.smtp.encryption' => $emailSettings->smtp_encryption,
                'mail.from.address' => $emailSettings->from_email,
                'mail.from.name' => $emailSettings->from_name
            ]);

            return response()->json([
                'error' => '',
                'settings' => $request->all()
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao salvar configurações: ' . $e->getMessage()]);
        }
    }

    public function testEmailSettings(Request $request)
    {
        try {
            Mail::raw('Email de teste - Configurações do sistema', function($message) {
                $settings = Setting::first();
                $emailSettings = json_decode($settings->email_settings);
                
                $message->to($emailSettings->from_email)
                        ->subject('Teste de Configuração de Email');
            });

            return response()->json(['error' => '']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao enviar email: ' . $e->getMessage()]);
        }
    }
 

    public function getAppearanceSettings()
    {           

       $settings = Cache::rememberForever('settings', function () {
           return Setting::first();
       });       
       
       if (!$settings || !$settings->appearance_settings) {
           return response()->json([
               'error' => '',
               'settings' => [
                   'theme' => 'light',
                   'primaryColor' => '#321fdb',
                   'sidebarStyle' => 'light', 
                   'menuCompact' => false,
                   'headerFixed' => true,
                   'customLogo' => null
               ]
           ]);
       }
    
       $settings = json_decode($settings->appearance_settings);
       if ($settings->customLogo) {
           $settings->customLogo = asset($settings->customLogo);
       }
    
       return response()->json([
           'error' => '',
           'settings' => $settings
       ]);
    }

   public function updateAppearanceSettings(Request $request)
   {
       $validator = Validator::make($request->all(), [
           'theme' => 'required|in:light,dark',
           'primaryColor' => 'required|string',
           'sidebarStyle' => 'required|in:light,dark',
           'menuCompact' => 'boolean',
           'headerFixed' => 'boolean',
           'customLogo' => 'nullable|string'
       ]);
   
       if ($validator->fails()) {
           return response()->json(['error' => $validator->errors()->first()]);
       }
   
       try {
           $settings = Setting::firstOrNew();
           $data = $request->all();
           
           if (!empty($data['customLogo']) && strpos($data['customLogo'], 'data:image') === 0) {
               $base64 = substr($data['customLogo'], strpos($data['customLogo'], ',') + 1);
               $extension = explode('/', mime_content_type($data['customLogo']))[1];
               $filename = 'logo_' . time() . '.' . $extension;
               
               Storage::put('public/images/' . $filename, base64_decode($base64));
               $data['customLogo'] = Storage::url('public/images/' . $filename);
           }
   
           $settings->appearance_settings = json_encode($data);
           $settings->save();
           Cache::forget('settings');
   
           return response()->json([
               'error' => '',
               'settings' => $data
           ]);
       } catch (\Exception $e) {
           return response()->json(['error' => 'Erro ao salvar configurações: ' . $e->getMessage()]);
       }
   }
}