<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;


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
    LostAndFoundController,
    ReservationController,
    UnitController,
    UserController,
    WallController,
    WarningController,
    ClassifiedsController,
    GalleryController,
    VideoController,
    NewsController,
    PollsController,
    ServiceProvidersController,
    BenefitsController,
    FolderController,
    ProfileController,
    CategoryController,
    ResetBaseController,
    PagesController,
    ContactController,
    SimpleMailController,
    DashboardController,
    VisitSiteLogController,
    WebhookController,
    IntegrationsController,
    SettingsController,
    MailController
 



};
use App\Models\Benefits;
use App\Models\Category;

Route::get('/ping', function () {
    return ['pong' => true];
});

Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');


Route::get('/admin/settings/company', [SettingsController::class, 'getCompanySettings']);
Route::get('/admin/settings/appearance', [SettingsController::class, 'getAppearanceSettings']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/admin/auth/login', [AuthController::class, 'loginAdmin']);
Route::post('/admin/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/admin/auth/reset-password', [AuthController::class, 'reset'])->name('password.reset');
Route::post('/front/auth/login', [AuthController::class, 'loginUser']);
Route::post('/front/auth/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/front/auth/reset-password', [AuthController::class, 'reset'])->name('password.reset');

Route::get('/front/new/{id}', [NewsController::class, 'getById']); // Obter uma notícia específica
Route::get('/front/news', [NewsController::class, 'getAllPublic']); // Obter uma notícia específica

Route::get('/front/classifieds', [ClassifiedsController::class, 'getAll']); // Listar todos os classificados
Route::get('/front/classified/{id}', [ClassifiedsController::class, 'getById']); // Obter um classificado específico

Route::get('/front/categories/{type?}', [CategoryController::class, 'getAll'])->where('type', '.*');


Route::get('/front/page/{slug}', [PagesController::class, 'getBySlugPublic']); // Obter uma notícia específica
Route::get('/front/page/private/{slug}', [PagesController::class, 'getBySlugPrivate']); // Obter uma notícia específica



Route::get('/front/menupublic', [PagesController::class, 'getAllPublicAndMenu']); // Obter uma notícia específica
Route::get('/front/menuprivate', [PagesController::class, 'getAllPrivateAndMenu']); // Obter uma notícia específica

Route::get('/front/pages', [PagesController::class, 'getAllPublic']); // Obter todas as paginas 
Route::get('/front/pages/private', [PagesController::class, 'getAllPrivate']); // Obter todas as paginas 


Route::get('/front/wall/{id}', [WallController::class, 'getById']); // Obter uma notícia específica
Route::get('/front/walls', [WallController::class, 'getAllPublic']);
Route::get('/front/walls/events', [WallController::class, 'getAllEventsPublic']);



Route::get('/front/classifieds', [ClassifiedsController::class, 'getAll']); // Listar todos os classificados
Route::get('/front/classified/{id}', [ClassifiedsController::class, 'getById']); // Obter um classificado específico
Route::get('/front/categories/{type?}', [CategoryController::class, 'getAll'])->where('type', '.*');


Route::get('/front/warnings/{id}', [WarningController::class, 'getAllByUserId']);
Route::delete('/front/warning/midia/{id}', [ClassifiedsController::class, 'deleteMidia']);
Route::get('/front/classified/user/{id}', [ClassifiedsController::class, 'getAllByUserId']); 

Route::get('/front/benefit/{id}', [BenefitsController::class, 'getById']); // Obter um Beneficios específico

Route::get('/front/docs', [DocController::class, 'getAllPublic']);// Obter Documentos



Route::get('/front/galleries', [GalleryController::class, 'getAllPublic']); // Listar todas as fotos
Route::post('/front/send-email-contact', [ContactController::class, 'sendEmailContact']);
Route::get('/test-email', [ContactController::class, 'testEmail']);
Route::get('/sendmail', [SimpleMailController::class, 'index']);

Route::post('/front/visits', [VisitSiteLogController::class, 'visits']);


//Route::post('/admin/auth/validate', [AuthController::class, 'validateToken']);
Route::middleware('auth:api')->group(function () {



    // User Routes
    Route::get('/front/user/{id}', [UserController::class, 'getById']);
    Route::post('/front/user/{id}', [UserController::class, 'updateByUser']);

    // Profile Routes
    Route::get('/front/profiles', [ProfileController::class, 'getAll']);

    // Classified Routes
    Route::post('/front/classified', [ClassifiedsController::class, 'insert']); // Criar um novo classificado
    Route::post('/front/classified/{id}', [ClassifiedsController::class, 'update']); // Atualizar um classificado existente
    Route::delete('/front/classified/{id}', [ClassifiedsController::class, 'delete']); // Excluir um classificado
    Route::post('/front/classified/midia/{id}', [ClassifiedsController::class, 'insertMidia']); // Inserir uma nova mídia
    Route::delete('/front/classified/midia/{id}', [ClassifiedsController::class, 'deleteMidia']); // Deletar uma  mídia

    // Warning Routes
    Route::post('/front/warning/{id}', [WarningController::class, 'update']);
    Route::post('/front/warning', [WarningController::class, 'insert']);
    Route::delete('/front/warning/{id}', [WarningController::class, 'delete']);
    

    //videos
    Route::get('/front/menuprivate', [PagesController::class, 'getAllPrivateAndMenu']); // Obter uma notícia específica
    Route::get('/front/videosprivate', [VideoController::class, 'getAllPrivate']); // Listar todas as fotos


    // Folder Routes
   
    Route::get('/front/folder/{id}', [FolderController::class, 'getById']); // Listar pasta específica
    Route::get('/front/folders', [FolderController::class, 'getAllPublic']);



       /**Achados e Perdidos    * **/
       Route::get('/front/lost-and-found/user/{id}', [LostAndFoundController::class, 'getAllUserId']); // Listar todos os achados e perdidos
       Route::get('/front/lost-and-found/{id}', [LostAndFoundController::class, 'getById']); // Obter um achado e perdido específico
       Route::post('/front/lost-and-found', [LostAndFoundController::class, 'insert']); // Criar um novo achado e perdido
       Route::post('/front/lost-and-found/{id}', [LostAndFoundController::class, 'update']); // Atualizar um achado e perdido existente
       Route::delete('/front/lost-and-found/{id}', [LostAndFoundController::class, 'delete']); // Excluir um achado e perdido
       Route::post('/front/lost-and-found/midia/{id}', [LostAndFoundController::class, 'insertMidia']); // Inserir uma nova mídia
       Route::delete('/front/lost-and-found/midia/{id}', [LostAndFoundController::class, 'deleteMidia']); // Deletar uma  mídia


       /**Cadastrar Prestadores de Serviço   * **/
       Route::get('/front/service-providers', [ServiceProvidersController::class, 'getAllActive']); // Criar um novo prestador de serviços
       Route::post('/front/service-providers', [ServiceProvidersController::class, 'insert']); // Criar um novo prestador de serviços




       
       // Admin Routes
    Route::get('/admin/migrate', [ResetBaseController::class, 'migrate']);
    Route::get('/admin/seed', [ResetBaseController::class, 'seed']);


Route::get('/admin/online-users', [DashboardController::class, 'getLastOnlineUsers']);
Route::get('/admin/access-stats', [DashboardController::class, 'getAccessStats']);

    /**USERS**/
    Route::get('/admin/users', [UserController::class, 'getAll']);
    Route::get('/admin/user/{id}', [UserController::class, 'getById']);
    Route::get('/admin/users/cpf/{cpf}', [UserController::class, 'getByCpf']);
    Route::post('/admin/user/{id}', [UserController::class, 'update']);
    Route::post('/admin/user', [UserController::class, 'insert']);
    Route::delete('/admin/user/{id}', [UserController::class, 'delete']);

    /**<--PERFIL DE ACESSO-->*/
    Route::get('/admin/profiles', [ProfileController::class, 'getAll']);
    Route::get('/admin/profile/{id}', [ProfileController::class, 'getById']);
    Route::post('/admin/profile/{id}', [ProfileController::class, 'update']);
    Route::post('/admin/profile', [ProfileController::class, 'insert']);
    Route::post('/admin/profile/{id}/status', [ProfileController::class, 'updateStatus']);
    Route::delete('/admin/profile/{id}', [ProfileController::class, 'delete']);


    Route::post('/auth/validate', [AuthController::class, 'validateToken']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/front/auth/validate', [AuthController::class, 'validateToken']);
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
    Route::post('/admin/documento/assembleia/{id}', [DocumentosAssembleiaController::class, 'update']);
    Route::post('/admin/documento/assembleia', [DocumentosAssembleiaController::class, 'insert']);
    Route::delete('/admin/documento/assembleia/{id}', [DocumentosAssembleiaController::class, 'delete']);
    Route::post('/admin/documento/assembleia/{id}/status', [DocumentosAssembleiaController::class, 'updateStatus']);





    /**CONDOMINIOS**/
    Route::get('/admin/condominios', [CondominiosController::class, 'getAll']);
    Route::post('/admin/condominio/{id}', [CondominiosController::class, 'update']);
    Route::post('/admin/condominios', [CondominiosController::class, 'insert']);
    Route::delete('/admin/condominio/{id}', [CondominiosController::class, 'delete']);
    /**<--CONDOMINIOS-->*/

    /**WALLS**/
    Route::get('/admin/walls', [WallController::class, 'getAll']);
    Route::get('/admin/wall/{id}', [WallController::class, 'getById']);
    Route::post('/admin/wall/{id}', [WallController::class, 'update']);
    Route::post('/admin/wall', [WallController::class, 'insert']);
    Route::post('/admin/wall/{id}/status', [WallController::class, 'updateStatus']);
    Route::delete('/admin/wall/{id}', [WallController::class, 'delete']);
    /**<--WALLS-->*/

    /**Categorias**/
    Route::get('/admin/categories/{type?}', [CategoryController::class, 'getAll'])->where('type', '.*');
    Route::get('/admin/category/{id}', [CategoryController::class, 'getById']);
    Route::post('/admin/category/{id}', [CategoryController::class, 'update']);
    Route::post('/admin/category', [CategoryController::class, 'insert']);
    Route::post('/admin/category/{id}/status', [CategoryController::class, 'updateStatus']);
    Route::delete('/admin/category/{id}', [CategoryController::class, 'delete']);


    /**DOCS**/
    Route::get('/admin/docs', [DocController::class, 'getAll']);
    Route::get('/admin/docs/category', [DocController::class, 'getAllCategory']);// Obter Documentos Categorias

    Route::get('/admin/doc/{id}', [DocController::class, 'getById']);
    Route::post('/admin/doc/{id}', [DocController::class, 'update']);
    Route::post('/admin/doc', [DocController::class, 'insert']);
    Route::delete('/admin/doc/{id}', [DocController::class, 'delete']);
    /**<--DOCS-->*/


    /**DOCS**/
    Route::get('/admin/folders', [FolderController::class, 'getAll']);
    Route::get('/admin/folder/{id}', [FolderController::class, 'getById']);
    Route::post('/admin/folder/{id}', [FolderController::class, 'update']);
    Route::post('/admin/folder', [FolderController::class, 'insert']);
    Route::post('/admin/folder/file/{id}', [FolderController::class, 'insertMidia']);
    Route::delete('/admin/file/{id}', [FolderController::class, 'deleteMidia']);
    Route::delete('/admin/folder/{id}', [FolderController::class, 'delete']);
    /**<--DOCS-->*/

    /**BOLETOS**/
    Route::get('/admin/billets', [BilletController::class, 'getAll']);
    Route::get('/admin/billet/{id}', [BilletController::class, 'getById']);
    Route::post('/admin/billet/{id}', [BilletController::class, 'update']);
    Route::post('/admin/billet', [BilletController::class, 'insert']);
    Route::delete('/admin/billet/{id}', [BilletController::class, 'delete']);

    /**Ocorrencias**/
    Route::get('/admin/warnings', [WarningController::class, 'getAll']);
    Route::get('/admin/warning/{id}', [WarningController::class, 'getById']);
    Route::post('/admin/warning/{id}', [WarningController::class, 'update']);
    Route::post('/admin/warning', [WarningController::class, 'insert']);
    Route::delete('/admin/warning/{id}', [WarningController::class, 'delete']);
    Route::delete('/admin/warning/midia/{id}', [ClassifiedsController::class, 'deleteMidia']); // Deletar uma  mídia



    /**Achados e Perdidos    * **/
    Route::get('/admin/lost-and-found', [LostAndFoundController::class, 'getAll']); // Listar todos os achados e perdidos
    Route::get('/admin/lost-and-found/{id}', [LostAndFoundController::class, 'getById']); // Obter um achado e perdido específico
    Route::post('/admin/lost-and-found', [LostAndFoundController::class, 'insert']); // Criar um novo achado e perdido
    Route::post('/admin/lost-and-found/{id}', [LostAndFoundController::class, 'update']); // Atualizar um achado e perdido existente
    Route::delete('/admin/lost-and-found/{id}', [LostAndFoundController::class, 'delete']); // Excluir um achado e perdido
    Route::post('/admin/lost-and-found/midia/{id}', [LostAndFoundController::class, 'insertMidia']); // Inserir uma nova mídia
    Route::delete('/admin/lost-and-found/midia/{id}', [LostAndFoundController::class, 'deleteMidia']); // Deletar uma  mídia


    /**AREAS**/

    Route::get('/admin/areas', [AreaController::class, 'getAll']);
    Route::get('/admin/area/{id}', [AreaController::class, 'getById']); // Obter um achado e perdido específico
    Route::post('/admin/area', [AreaController::class, 'insert']);
    Route::post('/admin/area/{id}', [AreaController::class, 'update']);
    Route::delete('/admin/area/{id}', [AreaController::class, 'delete']);
    Route::post('/admin/area/midia/{id}', [AreaController::class, 'insertMidia']); // Inserir uma nova mídia
    Route::delete('/admin/area/midia/{id}', [AreaController::class, 'deleteMidia']); // Deletar uma  mídia


    /**RESERVARTIONS**/
    Route::get('/admin/reservations', [ReservationController::class, 'getAll']);
    Route::get('/admin/reservation/{id}', [ReservationController::class, 'getById']); // Obter uma reserva específica
    Route::post('/admin/reservation', [ReservationController::class, 'insert']);
    Route::post('/admin/reservation/{id}', [ReservationController::class, 'update']);

    Route::delete('/admin/reservation/{id}', [ReservationController::class, 'delete']);


    /**<--RESERVERTIONS-->*/


    /**UNITS**/
    Route::get('/admin/units', [UnitController::class, 'getAll']);
    Route::get('/admin/unit/owner/{id}', [UnitController::class, 'getUnitByOwner']);
    Route::get('/admin/unit/{id}', [UnitController::class, 'getById']);
    Route::post('/admin/unit', [UnitController::class, 'insert']);
    Route::delete('/admin/unit/{id}', [UnitController::class, 'delete']);
    Route::put('/admin/unit/{id}', [UnitController::class, 'updateUnit']);


    /**<--UNITS-->*/

    /**<--CLASSIFICADOS-->*/

    Route::get('/admin/classifieds', [ClassifiedsController::class, 'getAll']); // Listar todos os classificados
    Route::get('/admin/classified/{id}', [ClassifiedsController::class, 'getById']); // Obter um classificado específico
    Route::post('/admin/classified', [ClassifiedsController::class, 'insert']); // Criar um novo classificado
    Route::post('/admin/classified/{id}', [ClassifiedsController::class, 'update']); // Atualizar um classificado existente
    Route::delete('/admin/classified/{id}', [ClassifiedsController::class, 'delete']); // Excluir um classificado
    Route::post('/admin/classified/midia/{id}', [ClassifiedsController::class, 'insertMidia']); // Inserir uma nova mídia
    Route::delete('/admin/classified/midia/{id}', [ClassifiedsController::class, 'deleteMidia']); // Deletar uma  mídia






    //*********************************************************************************************** */


    Route::get('/admin/news', [NewsController::class, 'getAll']); // Listar todas as notícias
    Route::get('/admin/news/{id}', [NewsController::class, 'getById']); // Obter uma notícia específica
    Route::post('/admin/new', [NewsController::class, 'insert']); // Criar uma nova notícia
    Route::post('/admin/new/{id}', [NewsController::class, 'update']); // Atualizar uma notícia existente
    Route::post('/admin/new/{id}/status', [NewsController::class, 'updateStatus']);
    Route::delete('/admin/new/{id}', [NewsController::class, 'delete']); // Excluir uma notícia


     //*********************************************************************************************** */


     Route::get('/admin/pages', [PagesController::class, 'getAll']); // Listar todas as notícias
     Route::get('/admin/page/{id}', [PagesController::class, 'getById']); // Obter uma notícia específica
     Route::post('/admin/page', [PagesController::class, 'insert']); // Criar uma nova notícia
     Route::post('/admin/page/{id}', [PagesController::class, 'update']); // Atualizar uma notícia existente
     Route::post('/admin/page/{id}/status', [PagesController::class, 'updateStatus']);
     Route::delete('/admin/page/{id}', [PagesController::class, 'delete']); // Excluir uma notícia

    /**<--Galeria-->*/

    Route::get('/admin/galleries', [GalleryController::class, 'getAll']); // Listar todas as fotos
    Route::get('/admin/gallery/{id}', [GalleryController::class, 'getById']); // Obter uma foto específica
    Route::post('/admin/gallery', [GalleryController::class, 'insert']); // Carregar uma nova foto
    Route::post('/admin/gallery/{id}', [GalleryController::class, 'update']); // Atualizar uma foto existente
    Route::delete('/admin/gallery/{id}', [GalleryController::class, 'delete']); // Excluir uma foto
    Route::post('/admin/gallery/midia/{id}', [GalleryController::class, 'insertMidia']); // Inserir uma nova mídia
    Route::delete('/admin/gallery/midia/{id}', [GalleryController::class, 'deleteMidia']); // Deletar uma  mídia


        /**<--Galeria-->*/

        Route::get('/admin/videos', [VideoController::class, 'getAll']); // Listar todas as fotos
        Route::get('/admin/video/{id}', [VideoController::class, 'getById']); // Obter uma foto específica
        Route::post('/admin/video', [VideoController::class, 'insert']); // Carregar uma nova foto
        Route::post('/admin/video/{id}', [VideoController::class, 'update']); // Atualizar uma foto existente
        Route::delete('/admin/video/{id}', [VideoController::class, 'delete']); // Excluir uma foto
        Route::post('/admin/video/midia/{id}', [VideoController::class, 'insertMidia']); // Inserir uma nova mídia
        Route::delete('/admin/video/midia/{id}', [VideoController::class, 'deleteMidia']); // Deletar uma  mídia

    /**<--Enquetes-->*/

    Route::get('/admin/polls', [PollsController::class, 'getAll']); // Listar todas as enquetes
    Route::get('/admin/poll/{id}', [PollsController::class, 'getById']); // Obter uma enquete específica
    Route::post('/admin/poll', [PollsController::class, 'insert']); // Criar uma nova enquete
    Route::post('/admin/poll/{id}', [PollsController::class, 'update']); // Atualizar uma enquete existente
    Route::delete('/admin/poll/{id}', [PollsController::class, 'delete']); // Excluir uma enquete

    /**<--Enquetes Perguntas-->*/

    Route::get('/admin/polls/questions/{id}', [PollsController::class, 'getQuestionsAll']); // Listar todas as opções de uma enquete
    Route::get('/admin/poll/question/{id}', [PollsController::class, 'getQuestionById']); // Obter uma opções específica
    Route::post('/admin/poll/{id}/question', [PollsController::class, 'insertQuestion']); // Criar uma nova opções realacionada a uma enquete
    Route::post('/admin/poll/question/{id}', [PollsController::class, 'updateQuestion']); // Atualizar uma opções existente
    Route::delete('/admin/poll/question/{id}', [PollsController::class, 'deleteQuestion']); // Excluir uma opções
    Route::post('/admin/poll/{id}/status', [PollsController::class, 'updateStatus']);


    /**<--Enquetes Respostas-->*/

    Route::get('/admin/poll/{id}/answers', [PollsController::class, 'getAnswersByPoll']); // Listar todas as respostas de uma enquete
    Route::post('/admin/poll/{id}/answer', [PollsController::class, 'insertAnswer']); // Criar uma nova respostas realacionada a uma enquete




    Route::get('/admin/service-providers', [ServiceProvidersController::class, 'getAll']); // Listar todos os prestadores de serviços
    Route::get('/admin/service-providers/{id}', [ServiceProvidersController::class, 'getById']); // Obter um prestador de serviços específico
    Route::post('/admin/service-providers', [ServiceProvidersController::class, 'insert']); // Criar um novo prestador de serviços
    Route::post('/admin/service-providers/{id}', [ServiceProvidersController::class, 'update']); // Atualizar um prestador de serviços existente
    Route::delete('/admin/service-providers/{id}', [ServiceProvidersController::class, 'delete']); // Excluir um prestador de serviços
    Route::post('/admin/service-providers/{id}/status', [ServiceProvidersController::class, 'updateStatus']);



    Route::get('/admin/benefits', [BenefitsController::class, 'getAll']); // Listar todos os Beneficios
    Route::get('/admin/benefit/{id}', [BenefitsController::class, 'getById']); // Obter um Beneficios específico
    Route::post('/admin/benefit', [BenefitsController::class, 'insert']); // Criar um novo Beneficios
    Route::post('/admin/benefit/{id}', [BenefitsController::class, 'update']); // Atualizar um Beneficio existente
    Route::post('/admin/benefit/{id}/status', [BenefitsController::class, 'updateStatus']);
    Route::delete('/admin/benefit/{id}', [BenefitsController::class, 'delete']); // Excluir um Beneficio

    Route::get('/admin/visitas', [BenefitsController::class, 'getAll']); // Listar todos os Beneficios
    Route::get('/admin/visita/{id}', [BenefitsController::class, 'getById']); // Obter um Beneficios específico
    Route::post('/admin/visita', [BenefitsController::class, 'insert']); // Criar um novo Beneficios
    Route::post('/admin/visita/{id}', [BenefitsController::class, 'update']); // Atualizar um Beneficio existente
    Route::post('/admin/visita/{id}/status', [BenefitsController::class, 'updateStatus']);
    Route::delete('/admin/visita/{id}', [BenefitsController::class, 'delete']); // Excluir um Beneficio











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
    Route::get('/foundandlost', [LostAndFoundController::class, 'getAll']);
    Route::post('/foundandlost', [LostAndFoundController::class, 'insert']);
    Route::put('/foundandlost/{id}', [LostAndFoundController::class, 'update']);


    Route::post('/admin/sendmail', [MailController::class, 'enviarEmail']);

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


});
