<?php

namespace App\Http\Controllers;

use App\Models\Local;
use Illuminate\Support\Facades\Log;

class PublicController extends Controller
{
    public function welcome()
    {
        $local = null;
        try {
            try {
                $local = Local::query()->orderBy('loc_id')->first();
            } catch (\Throwable $e) {
                Log::warning('welcome: não foi possível carregar local de exemplo', [
                    'exception' => $e->getMessage(),
                ]);
            }

            // render() aqui: exceções do Blade/layout (ex.: @vite) ocorrem dentro deste try;
            // apenas `return view()` adia a renderização até depois do controller (500 sem catch).
            return response(view('welcome', compact('local'))->render());
        } catch (\Throwable $e) {
            report($e);

            try {
                return response(view('welcome-fallback', [])->render(), 200);
            } catch (\Throwable $e2) {
                report($e2);

                return response(view('welcome-fallback-minimal', compact('local'))->render(), 200);
            }
        }
    }
}
