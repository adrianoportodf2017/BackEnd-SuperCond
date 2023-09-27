<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
    AreaController,
    AssembleiaController,
    DocumentosAssembleiaController,
    CondominiosController,
    BilletController,
    DocController,
    FoundAndLostController,
    ReservationController,
    UnitController,
    UserController,
    WallController,
    WarningController,
};

Route::get('/ping', function () {
    return ['pong' => true];
});

Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');


Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/admin/auth/login', [AuthController::class, 'loginAdmin']);
Route::post('/admin/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/admin/auth/reset-password', [AuthController::class, 'reset'])->name('password.reset');
//Route::post('/admin/auth/validate', [AuthController::class, 'validateToken']);
Route::middleware('auth:api')->group(function () {


    Route::post('/auth/validate', [AuthController::class, 'validateToken']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/admin/auth/validate', [AuthController::class, 'validateToken']);
    Route::post('/admin/auth/logout', [AuthController::class, 'logout']);

    /**ASSEMBLEIAS**/
    Route::get('/admin/assembleias', [AssembleiaController::class, 'getAll']);
    Route::post('/admin/assembleia/{id}', [AssembleiaController::class, 'update']);
    Route::post('/admin/assembleia', [AssembleiaController::class, 'insert']);
    Route::delete('/admin/assembleia/{id}', [AssembleiaController::class, 'delete']);
    Route::post('/admin/assembleia/{id}/status', [AssembleiaController::class, 'updateStatus']);

    /**DOCUMENTOS ASSEMBLEIAS**/
    Route::get('/admin/documentos/assembleias', [DocumentosAssembleiaController::class, 'getAll']);
    Route::get('/admin/documentos/assembleia/{id}/documentos', [DocumentosAssembleiaController::class, 'getDocumentosAssembleia']);
    Route::post('/admin/documentos/assembleia/{id}', [DocumentosAssembleiaController::class, 'update']);
    Route::post('/admin/documentos/assembleia', [DocumentosAssembleiaController::class, 'insert']);
    Route::delete('/admin/documentos/assembleia/{id}', [DocumentosAssembleiaController::class, 'delete']);
    Route::post('/admin/documentos/assembleia/{id}/status', [DocumentosAssembleiaController::class, 'updateStatus']);





    /**CONDOMINIOS**/
    Route::get('/admin/condominios', [CondominiosController::class, 'getAll']);
    Route::post('/admin/condominio/{id}', [CondominiosController::class, 'update']);
    Route::post('/admin/condominios', [CondominiosController::class, 'insert']);
    Route::delete('/admin/condominio/{id}', [CondominiosController::class, 'delete']);
    /**<--CONDOMINIOS-->*/

    /**WALLS**/
    Route::get('/admin/walls', [WallController::class, 'getAll']);
    Route::put('/admin/wall/{id}', [WallController::class, 'update']);
    Route::post('/admin/walls', [WallController::class, 'insert']);
    Route::delete('/admin/wall/{id}', [WallController::class, 'delete']);
    /**<--WALLS-->*/


    /**DOCS**/
    Route::get('/admin/docs', [DocController::class, 'getAll']);
    Route::post('/admin/doc/{id}', [DocController::class, 'update']);
    Route::post('/admin/docs', [DocController::class, 'insert']);
    Route::delete('/admin/doc/{id}', [DocController::class, 'delete']);
    /**<--DOCS-->*/


    /**RESERVARTIONS**/
    Route::get('/admin/reservations', [ReservationController::class, 'getAll']);
    Route::post('/admin/reservations', [ReservationController::class, 'insert']);
    Route::put('/admin/reservation/{id}', [ReservationController::class, 'update']);
    Route::delete('/admin/reservation/{id}', [ReservationController::class, 'delete']);
    /**<--RESERVERTIONS-->*/


    /**UNITS**/
    Route::get('/admin/units', [UnitController::class, 'getAll']);
    Route::get('/unit/{id}', [UnitController::class, 'getInfo']);
    Route::post('/admin/unit/{id}', [UnitController::class, 'updateUnit']);
    Route::post('/unit/{id}/addperson', [UnitController::class, 'addPerson']);
    Route::post('/unit/{id}/addvehicle', [UnitController::class, 'addVehicle']);
    Route::post('/unit/{id}/addpet', [UnitController::class, 'addPet']);
    Route::post('/unit/{id}/removeperson', [UnitController::class, 'removePerson']);
    Route::post('/unit/{id}/removevehicle', [UnitController::class, 'removeVehicle']);
    Route::post('/unit/{id}/removepet', [UnitController::class, 'removePet']);
    /**<--UNITS-->*/

    /**AREAS**/
    Route::get('/admin/areas', [AreaController::class, 'getAll']);
    Route::post('/admin/areas', [AreaController::class, 'insert']);
    Route::post('/admin/area/{id}', [AreaController::class, 'update']);
    Route::delete('/admin/area/{id}', [AreaController::class, 'delete']);


    Route::get('/admin/news', [NewsController::class, 'index']); // Listar todas as notícias
    Route::get('/admin/news/{id}', [NewsController::class, 'show']); // Obter uma notícia específica
    Route::post('/admin/news', [NewsController::class, 'store']); // Criar uma nova notícia
    Route::put('/admin/news/{id}', [NewsController::class, 'update']); // Atualizar uma notícia existente
    Route::delete('/admin/news/{id}', [NewsController::class, 'destroy']); // Excluir uma notícia


    Route::get('/admin/classifieds', [ClassifiedsController::class, 'index']); // Listar todos os classificados
    Route::get('/admin/classifieds/{id}', [ClassifiedsController::class, 'show']); // Obter um classificado específico
    Route::post('/admin/classifieds', [ClassifiedsController::class, 'store']); // Criar um novo classificado
    Route::put('/admin/classifieds/{id}', [ClassifiedsController::class, 'update']); // Atualizar um classificado existente
    Route::delete('/admin/classifieds/{id}', [ClassifiedsController::class, 'destroy']); // Excluir um classificado

    Route::get('/admin/photos', [PhotosController::class, 'index']); // Listar todas as fotos
    Route::get('/admin/photos/{id}', [PhotosController::class, 'show']); // Obter uma foto específica
    Route::post('/admin/photos', [PhotosController::class, 'store']); // Carregar uma nova foto
    Route::put('/admin/photos/{id}', [PhotosController::class, 'update']); // Atualizar uma foto existente
    Route::delete('/admin/photos/{id}', [PhotosController::class, 'destroy']); // Excluir uma foto


    Route::get('/admin/partners', [PartnersController::class, 'index']); // Listar todos os convênios/parceiros
    Route::get('/admin/partners/{id}', [PartnersController::class, 'show']); // Obter um convênio/parceiro específico
    Route::post('/admin/partners', [PartnersController::class, 'store']); // Criar um novo convênio/parceiro
    Route::put('/admin/partners/{id}', [PartnersController::class, 'update']); // Atualizar um convênio/parceiro existente
    Route::delete('/admin/partners/{id}', [PartnersController::class, 'destroy']); // Excluir um convênio/parceiro

    Route::get('/admin/polls', [PollsController::class, 'index']); // Listar todas as enquetes
    Route::get('/admin/polls/{id}', [PollsController::class, 'show']); // Obter uma enquete específica
    Route::post('/admin/polls', [PollsController::class, 'store']); // Criar uma nova enquete
    Route::put('/admin/polls/{id}', [PollsController::class, 'update']); // Atualizar uma enquete existente
    Route::delete('/admin/polls/{id}', [PollsController::class, 'destroy']); // Excluir uma enquete

    Route::get('/admin/reservations', [ReservationsController::class, 'index']); // Listar todas as reservas
    Route::get('/admin/reservations/{id}', [ReservationsController::class, 'show']); // Obter uma reserva específica
    Route::post('/admin/reservations', [ReservationsController::class, 'store']); // Criar uma nova reserva
    Route::put('/admin/reservations/{id}', [ReservationsController::class, 'update']); // Atualizar uma reserva existente
    Route::delete('/admin/reservations/{id}', [ReservationsController::class, 'destroy']); // Excluir uma reserva

    Route::get('/admin/service-providers', [ServiceProvidersController::class, 'index']); // Listar todos os prestadores de serviços
    Route::get('/admin/service-providers/{id}', [ServiceProvidersController::class, 'show']); // Obter um prestador de serviços específico
    Route::post('/admin/service-providers', [ServiceProvidersController::class, 'store']); // Criar um novo prestador de serviços
    Route::put('/admin/service-providers/{id}', [ServiceProvidersController::class, 'update']); // Atualizar um prestador de serviços existente
    Route::delete('/admin/service-providers/{id}', [ServiceProvidersController::class, 'destroy']); // Excluir um prestador de serviços

    Route::get('/admin/incidents', [IncidentsController::class, 'index']); // Listar todas as ocorrências
    Route::get('/admin/incidents/{id}', [IncidentsController::class, 'show']); // Obter uma ocorrência específica
    Route::post('/admin/incidents', [IncidentsController::class, 'store']); // Criar uma nova ocorrência
    Route::put('/admin/incidents/{id}', [IncidentsController::class, 'update']); // Atualizar uma ocorrência existente
    Route::delete('/admin/incidents/{id}', [IncidentsController::class, 'destroy']); // Excluir uma ocorrência

    Route::get('/admin/lost-and-found', [LostAndFoundController::class, 'index']); // Listar todos os achados e perdidos
    Route::get('/admin/lost-and-found/{id}', [LostAndFoundController::class, 'show']); // Obter um achado e perdido específico
    Route::post('/admin/lost-and-found', [LostAndFoundController::class, 'store']); // Criar um novo achado e perdido
    Route::put('/admin/lost-and-found/{id}', [LostAndFoundController::class, 'update']); // Atualizar um achado e perdido existente
    Route::delete('/admin/lost-and-found/{id}', [LostAndFoundController::class, 'destroy']); // Excluir um achado e perdido



    /**USERS**/
    Route::get('/admin/users', [UserController::class, 'getAll']);
    Route::get('/admin/users/search', [UserController::class, 'search']);
    Route::post('/admin/user/{id}', [UserController::class, 'update']);
    Route::post('/admin/user', [UserController::class, 'insert']);
    Route::delete('/admin/user/{id}', [UserController::class, 'delete']);
    /**<--USERS-->*/


    Route::post('/auth/logout', [AuthController::class, 'logout']);



    /**
     * ************************
     * ************************
     * ************************
     * ************************
     * API PARA O USO DA  APP
     * ************************
     * ************************
     * */

    //Mural de Avisos
    Route::get('/walls', [WallController::class, 'getAll']);
    Route::get('/walls/{id}/like', [WallController::class, 'like']);


    //Documentos
    Route::get('/docs', [WallController::class, 'getAll']);

    //Livros de Ocorrencias
    Route::get('/warnings', [WarningController::class, 'getMyWarnings']);
    Route::post('/warnings', [WarningController::class, 'setWarning']);
    Route::post('/warnings/file', [WarningController::class, 'addWarningFile']);

    //boletos
    Route::get('/billets', [BilletController::class, 'getAll']);

    //achados e perdidos
    Route::get('/foundandlost', [FoundAndLostController::class, 'getAll']);
    Route::post('/foundandlost', [FoundAndLostController::class, 'insert']);
    Route::put('/foundandlost/{id}', [FoundAndLostController::class, 'update']);

    //Unidade
    Route::get('/unit/{id}', [UnitController::class, 'getInfo']);
    Route::post('/unit/{id}/addperson', [UnitController::class, 'addPerson']);
    Route::post('/unit/{id}/addvehicle', [UnitController::class, 'addVehicle']);
    Route::post('/unit/{id}/addpet', [UnitController::class, 'addPet']);
    Route::post('/unit/{id}/removeperson', [UnitController::class, 'removePerson']);
    Route::post('/unit/{id}/removevehicle', [UnitController::class, 'removeVehicle']);
    Route::post('/unit/{id}/removepet', [UnitController::class, 'removePet']);


    //Reservas
    Route::get('/reservations', [ReservationController::class, 'getReservations']);

    Route::get('/reservations/{id}/disableddates', [ReservationController::class, 'getDisabledDates']);
    Route::get('/reservations/{id}/times', [ReservationController::class, 'getTimes']);

    Route::get('/myreservations', [ReservationController::class, 'getMyReservations']);
    Route::post('/myreservations', [ReservationController::class, 'setMyReservations']);
    Route::delete('/myreservations/{id}', [ReservationController::class, 'delMyReservations']);
});
