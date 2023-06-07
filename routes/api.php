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
//Route::post('/admin/auth/validate', [AuthController::class, 'validateToken']);
Route::middleware('auth:api')->group(function () {


    Route::post('/auth/validate', [AuthController::class, 'validateToken']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/admin/auth/validate', [AuthController::class, 'validateToken']);
    Route::post('/admin/auth/logout', [AuthController::class, 'logout']);


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
    Route::post('/admin/reservation/{id}', [DocController::class, 'update']);
    Route::post('/admin/reservation', [DocController::class, 'insert']);
    Route::delete('/admin/reservation/{id}', [DocController::class, 'delete']);
    /**<--RESERVERTIONS-->*/



    /**USERS**/
    Route::get('/admin/users', [UserController::class, 'getAll']);
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
