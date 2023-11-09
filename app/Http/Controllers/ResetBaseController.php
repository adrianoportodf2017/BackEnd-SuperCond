<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;


class ResetBaseController extends Controller
{
      public function migrate()
    {
        Artisan::call('migrate:fresh');
        Artisan::call('db:seed');
        return response()->json(['message' => 'Migrações concluídas']);
    }
}