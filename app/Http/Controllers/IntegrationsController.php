<?php

namespace App\Http\Controllers;

use App\Models\Integration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class IntegrationsController extends Controller
{
   public function getPaymentSettings()
   {
       $stripeSettings = Integration::where('type', 'stripe')->first();
       $assasSettings = Integration::where('type', 'assas')->first();

       return response()->json([
           'error' => '',
           'stripe' => $stripeSettings ? json_decode($stripeSettings->settings) : [
               'enabled' => false,
               'test_mode' => true,
               'public_key' => '',
               'secret_key' => '',
               'webhook_secret' => '',
               'test_public_key' => '',
               'test_secret_key' => '',
               'test_webhook_secret' => ''
           ],
           'assas' => $assasSettings ? json_decode($assasSettings->settings) : [
               'enabled' => false,
               'test_mode' => true,
               'api_key' => '',
               'webhook_secret' => '',
               'test_api_key' => '',
               'test_webhook_secret' => ''
           ]
       ]);
   }

   public function updateStripeSettings(Request $request)
   {
       $validator = Validator::make($request->all(), [
           'enabled' => 'required|boolean',
           'test_mode' => 'required|boolean',
           'public_key' => 'required',
           'secret_key' => 'required',
           'webhook_secret' => 'required',
           'test_public_key' => 'required_if:test_mode,true|string',
           'test_secret_key' => 'required_if:test_mode,true|string',
           'test_webhook_secret' => 'required_if:test_mode,true|string'
       ]);

       if ($validator->fails()) {
           return response()->json(['error' => $validator->errors()->first()]);
       }

       try {
           $integration = Integration::firstOrNew(['type' => 'stripe']);
           $integration->settings = json_encode($request->all());
           $integration->is_active = $request->enabled;
           $integration->test_mode = $request->test_mode;
           $integration->save();

           return response()->json([
               'error' => '',
               'settings' => $request->all()
           ]);
       } catch (\Exception $e) {
           return response()->json(['error' => 'Erro ao salvar configurações: ' . $e->getMessage()]);
       }
   }

   public function updateAssasSettings(Request $request)
   {
       $validator = Validator::make($request->all(), [
           'enabled' => 'required|boolean',
           'test_mode' => 'required|boolean',
           'api_key' => 'required_if:test_mode,false|string',
           'webhook_secret' => 'required_if:test_mode,false|string',
           'test_api_key' => 'required_if:test_mode,true|string',
           'test_webhook_secret' => 'required_if:test_mode,true|string'
       ]);

       if ($validator->fails()) {
           return response()->json(['error' => $validator->errors()->first()]);
       }

       try {
           $integration = Integration::firstOrNew(['type' => 'assas']);
           $integration->settings = json_encode($request->all());
           $integration->is_active = $request->enabled;
           $integration->test_mode = $request->test_mode;
           $integration->save();

           return response()->json([
               'error' => '',
               'settings' => $request->all()
           ]);
       } catch (\Exception $e) {
           return response()->json(['error' => 'Erro ao salvar configurações: ' . $e->getMessage()]);
       }
   }

   public function testPaymentWebhook($gateway)
   {
       $integration = Integration::where('type', $gateway)->first();
       if (!$integration) {
           return response()->json(['error' => 'Integração não encontrada']);
       }

       $settings = json_decode($integration->settings);
       $webhookUrl = $integration->test_mode ? 
           ($gateway === 'stripe' ? $settings->test_webhook_secret : $settings->test_webhook_secret) :
           ($gateway === 'stripe' ? $settings->webhook_secret : $settings->webhook_secret);

       try {
           $response = Http::post($webhookUrl, [
               'test' => true,
               'event' => 'test.webhook',
               'timestamp' => time()
           ]);

           if ($response->successful()) {
               return response()->json(['error' => '']);
           }

           return response()->json(['error' => 'Erro no teste do webhook: ' . $response->body()]);
       } catch (\Exception $e) {
           return response()->json(['error' => 'Erro ao testar webhook: ' . $e->getMessage()]);
       }
   }
}