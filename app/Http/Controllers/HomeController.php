<?php

namespace App\Http\Controllers;

use App\Models\Drink;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke()
    {
        // Legacy Logic: "Today's Specials" = Latest 3 drinks
        $specials = Drink::latest()->take(3)->get();

        return view('home', compact('specials'));
    }
}
