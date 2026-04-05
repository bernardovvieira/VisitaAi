<?php

namespace App\Http\Controllers;

use App\Models\Local;
use App\Services\Public\WelcomePublicIndicadoresService;

class PublicController extends Controller
{
    public function welcome(WelcomePublicIndicadoresService $welcomeIndicadores)
    {
        $local = Local::first();
        $publicIndicadores = $welcomeIndicadores->resumo();

        return view('welcome', compact('local', 'publicIndicadores'));
    }
}
