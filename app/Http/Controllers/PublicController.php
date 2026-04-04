<?php

namespace App\Http\Controllers;

use App\Models\Local;

class PublicController extends Controller
{
    public function welcome()
    {
        $local = Local::first();

        return view('welcome', compact('local'));
    }
}
