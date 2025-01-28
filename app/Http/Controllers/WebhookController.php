<?php

namespace App\Http\Controllers;

use App\Models\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class WebhookController extends Controller
{
   public function index()
   {
       $webhooks = Webhook::all();
       return response()->json(['error' => '', 'webhooks' => $webhooks]);
   }

   public function store(Request $request)
   {
       $validator = Validator::make($request->all(), [
           'name' => 'required|string',
           'event' => 'required|string',
           'url' => 'required|url',
           'description' => 'nullable|string',
           'header' => 'nullable|array',
           'is_active' => 'boolean'
       ]);

       if ($validator->fails()) {
           return response()->json(['error' => $validator->errors()->first()]);
       }

       try {
           $webhook = Webhook::create([
               'name' => $request->name,
               'event' => $request->event,
               'url' => $request->url,
               'description' => $request->description,
               'headers' => json_encode($request->header),
               'is_active' => $request->is_active ?? true
           ]);

           return response()->json(['error' => '', 'webhook' => $webhook]);
       } catch (\Exception $e) {
           return response()->json(['error' => 'Erro ao criar webhook: ' . $e->getMessage()]);
       }
   }

   public function show($id)
   {
       try {
           $webhook = Webhook::findOrFail($id);
           return response()->json(['error' => '', 'webhook' => $webhook]);
       } catch (\Exception $e) {
           return response()->json(['error' => 'Webhook não encontrado']);
       }
   }

   public function update(Request $request, $id)
   {
       $validator = Validator::make($request->all(), [
           'name' => 'required|string',
           'event' => 'required|string',
           'url' => 'required|url',
           'description' => 'nullable|string',
           'header' => 'nullable',
           'is_active' => 'boolean'
       ]);

       if ($validator->fails()) {
           return response()->json(['error' => $validator->errors()->first()]);
       }

       try {
           $webhook = Webhook::findOrFail($id);
           $webhook->update([
               'name' => $request->name,
               'event' => $request->event,
               'url' => $request->url,
               'description' => $request->description,
               'headers' => json_encode($request->header),
               'is_active' => $request->is_active
           ]);

           return response()->json(['error' => '', 'webhook' => $webhook]);
       } catch (\Exception $e) {
           return response()->json(['error' => 'Erro ao atualizar webhook: ' . $e->getMessage()]);
       }
   }

   public function destroy($id)
   {
       try {
           $webhook = Webhook::findOrFail($id);
           $webhook->delete();
           return response()->json(['error' => '', 'message' => 'Webhook removido com sucesso']);
       } catch (\Exception $e) {
           return response()->json(['error' => 'Erro ao remover webhook: ' . $e->getMessage()]);
       }
   }

   public function testWebhook($id)
   {
       try {
           $webhook = Webhook::findOrFail($id);

           $response = Http::withHeaders(json_decode($webhook->headers) ?? [])
               ->post($webhook->url, [
                   'event' => 'test',
                   'timestamp' => now()->timestamp,
                   'data' => [
                       'message' => 'Teste de webhook',
                       'webhook_id' => $webhook->id
                   ]
               ]);

           return response()->json([
               'error' => '',
               'success' => $response->successful(),
               'status' => $response->status(),
               'response' => $response->json()
           ]);
       } catch (\Exception $e) {
           return response()->json(['error' => 'Erro ao testar webhook: ' . $e->getMessage()]);
       }
   }
}