<?php

namespace App\Http\Controllers;

use App\Models\Local;

class PublicController extends Controller
{
    public function welcome()
    {
        $local = Local::query()->orderBy('loc_id')->first();

        return view('welcome', compact('local'));
    }
}
