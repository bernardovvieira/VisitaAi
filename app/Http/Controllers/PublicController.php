<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Local;

class PublicController extends Controller
{
    public function welcome()
    {
        $local = Local::first(); // Pega o primeiro local cadastrado
        return view('welcome', compact('local'));
    }
}
