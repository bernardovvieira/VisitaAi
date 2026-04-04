<?php

namespace App\Http\Controllers;

use App\Models\Local;
use Illuminate\Support\Facades\Log;

class PublicController extends Controller
{
    public function welcome()
    {
        try {
            $local = null;
            try {
                $local = Local::query()->orderBy('loc_id')->first();
            } catch (\Throwable $e) {
                Log::warning('welcome: não foi possível carregar local de exemplo', [
                    'exception' => $e->getMessage(),
                ]);
            }

            return view('welcome', compact('local'));
        } catch (\Throwable $e) {
            report($e);

            return response()->view('welcome-fallback', [], 200);
        }
    }
}
