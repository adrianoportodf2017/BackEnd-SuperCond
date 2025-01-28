<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
use App\Http\Controllers\{
    AuthController,
    UserController,
    ProfileController,
    MailController,
    DashboardController,
    VisitSiteLogController,
    AssociadosController,
    ContratosController,
    SettingsController,
    IntegrationsController,
    WebhookController

};
use App\Models\Associados;

Route::get('/ping', function () {
    return ['pong' => true];
});




Route::get('/storage-link', function () {
    $publicStoragePath = public_path('storage');

    // Verifica se a pasta 'public/storage' já existe
    if (File::exists($publicStoragePath)) {
        // Remove a pasta 'public/storage'
        File::deleteDirectory($publicStoragePath);
    }

    // Executa o comando 'storage:link' para criar o link simbólico
    Artisan::call('storage:link');

    return 'Storage link created (and existing public/storage removed if it existed).';
});


Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');
Route::get('/admin/settings/company', [SettingsController::class, 'getCompanySettings']);

Route::get('/admin/settings/appearance', [SettingsController::class, 'getAppearanceSettings']);


Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/admin/auth/login', [AuthController::class, 'loginAdmin']);
Route::post('/admin/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/admin/auth/reset-password', [AuthController::class, 'reset'])->name('password.reset');
Route::get('/admin/profiles-public', [ProfileController::class, 'getAllPublic']);
Route::post('/admin/user-public', [UserController::class, 'insertPublic']);

Route::post('/admin/associado', [AssociadosController::class, 'insert']);


//Route::post('/admin/auth/validate', [AuthController::class, 'validateToken']);
Route::middleware('auth:api')->group(function () {


    Route::post('/admin/sendmail', [MailController::class, 'enviarEmail']);


    Route::get('/admin/online-users', [DashboardController::class, 'getLastOnlineUsers']);
    Route::get('/admin/access-stats', [DashboardController::class, 'getAccessStats']);

    /**USERS**/
    Route::get('/admin/users', [UserController::class, 'getAll']);
    Route::post('/admin/user', [UserController::class, 'insert']);
    Route::get('/admin/user/{id}', [UserController::class, 'getById']);
    Route::get('/admin/users/cpf/{cpf}', [UserController::class, 'getByCpf']);
    Route::post('/admin/user/{id}', [UserController::class, 'update']);
    Route::delete('/admin/user/{id}', [UserController::class, 'delete']);

    /**<--PERFIL DE ACESSO-->*/
    Route::get('/admin/profiles', [ProfileController::class, 'getAll']);
    Route::get('/admin/profile/{id}', [ProfileController::class, 'getById']);
    Route::post('/admin/profile/{id}', [ProfileController::class, 'update']);
    Route::post('/admin/profile', [ProfileController::class, 'insert']);
    Route::post('/admin/profile/{id}/status', [ProfileController::class, 'updateStatus']);
    Route::delete('/admin/profile/{id}', [ProfileController::class, 'delete']);

    Route::post('/admin/auth/validate', [AuthController::class, 'validateToken']);
    Route::post('/admin/auth/logout', [AuthController::class, 'logout']);

    /**<--PESSOA JURÍDICA-->*/
    Route::get('/admin/associados', [AssociadosController::class, 'getAll']);

    Route::get('/admin/associados/pj', [AssociadosController::class, 'getAllPJ']);
    Route::get('/admin/associados/pf', [AssociadosController::class, 'getAllPF']);

    Route::get('/admin/associado/{id}', [AssociadosController::class, 'getById']);
    Route::post('/admin/associado/{id}', [AssociadosController::class, 'update']);

    Route::post('/admin/associado/associacao/{id}', [AssociadosController::class, 'updateAssociacao']);


    Route::post('/admin/associado/{id}/status', [AssociadosController::class, 'updateStatus']);
    Route::delete('/admin/associado/{id}', [AssociadosController::class, 'delete']);


    Route::get('/admin/modelos/contratos', [ContratosController::class, 'getAll']);
    Route::post('/admin/modelo/contrato/{id}', [ContratosController::class, 'update']);
    Route::post('/admin/modelo/contrato', [ContratosController::class, 'insert']);
    Route::post('/admin/modelo/contrato/delete/{id}', [ContratosController::class, 'delete']);



    Route::post('/admin/contrato/{idAssociacao}', [ContratosController::class, 'insertContratoAssociacao']);


    /**DOCS**/
    //Route::get('/admin/docs', [DocController::class, 'getAll']);
    // Route::get('/admin/docs/category', [DocController::class, 'getAllCategory']);// Obter Documentos Categorias

    Route::get('/admin/docs/associacao/{id}', [AssociadosController::class, 'getDocsByAssociacaoId']);
    Route::post('/admin/docs/associacao/{id}', [AssociadosController::class, 'insertByAssociacaoId']);
    Route::post('/admin/docs/associacao/delete/{id}', [AssociadosController::class, 'deleteMidia']);
    Route::post('/admin/docs/associacao/email/{id}', [AssociadosController::class, 'enviarDocumentoEmail']);



    Route::get('/admin/docs/associacao/boletos/{id}', [AssociadosController::class, 'getBoletosByAssociacaoId']);
    Route::post('/admin/docs/associacao/boleto/{id}', [AssociadosController::class, 'insertBoletoByAssociacaoId']);
    Route::post('/admin/docs/associacao/email/boletos/{id}', [AssociadosController::class, 'enviarBoletoEmail']);
    Route::post('/admin/docs/associacao/email/contrato/{id}', [AssociadosController::class, 'enviarContratoEmail']);

    Route::post('/admin/docs/associacao/boleto/update/{id}', [AssociadosController::class, 'updateBoletoByAssociado']);

    Route::post('/admin/docs/associacao/boleto/delete/{id}', [AssociadosController::class, 'deleteBoleto']);

    /**SETTINGS**/
    // Company Settings
    Route::post('/admin/settings/company', [SettingsController::class, 'updateCompanySettings']);

    // Email Settings
    Route::get('/admin/settings/email', [SettingsController::class, 'getEmailSettings']);
    Route::post('/admin/settings/email', [SettingsController::class, 'updateEmailSettings']);
    Route::post('/admin/settings/email/test', [SettingsController::class, 'testEmailSettings']);

    // Appearance Settings   
    Route::post('/admin/settings/appearance', [SettingsController::class, 'updateAppearanceSettings']);

    /**INTEGRATIONS**/
    // Payment Settings
    Route::get('/admin/settings/payment', [IntegrationsController::class, 'getPaymentSettings']);
    Route::post('/admin/settings/payment/stripe', [IntegrationsController::class, 'updateStripeSettings']);
    Route::post('/admin/settings/payment/assas', [IntegrationsController::class, 'updateAssasSettings']);
    Route::post('/admin/settings/payment/{gateway}/test-webhook', [IntegrationsController::class, 'testPaymentWebhook']);

    // Webhooks
    Route::get('/admin/webhooks', [WebhookController::class, 'index']);
    Route::post('/admin/webhooks', [WebhookController::class, 'store']);
    Route::get('/admin/webhooks/{id}', [WebhookController::class, 'show']);
    Route::put('/admin/webhooks/{id}', [WebhookController::class, 'update']);
    Route::delete('/admin/webhooks/{id}', [WebhookController::class, 'destroy']);
    Route::post('/admin/webhooks/{id}/test', [WebhookController::class, 'testWebhook']);


    // Route::post('/admin/doc/{id}', [DocController::class, 'update']);
    // Route::post('/admin/doc', [DocController::class, 'insert']);
    //  Route::delete('/admin/doc/{id}', [DocController::class, 'delete']);

});
