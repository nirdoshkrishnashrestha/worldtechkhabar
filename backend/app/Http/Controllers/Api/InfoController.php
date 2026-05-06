<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class InfoController extends Controller
{
    public function __invoke()
    {
        return response()->json([
            'name' => 'World Tech Khabar API',
            'url' => 'https://worldtechkhabar.com',
            'admin' => url('/admin'),
        ]);
    }
}
