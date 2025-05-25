<?php

namespace App\Http\Controllers\TestingPurpose;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class Crypto extends Controller
{
    public function index()
    {
        $response = Http::get('https://api.coingecko.com/api/v3/coins/markets', [
            'vs_currency' => 'usd',
            'order' => 'market_cap_desc',
            'per_page' => 10,
            'page' => 1,
            'sparkline' => false,
        ]);

        $coins = $response->json();

        return view('test.index', compact('coins'));
    }
}
