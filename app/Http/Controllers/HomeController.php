<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Petshop;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Remova esta linha para permitir acesso público à página inicial
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Obter produtos em destaque de diferentes petshops
        $featuredProducts = Product::with('petshop')
                              ->where('is_active', true)
                              ->orderBy('created_at', 'desc')
                              ->take(8)
                              ->get();
        
        // Obter petshops em destaque
        $featuredPetshops = Petshop::where('is_active', true)
                              ->orderBy('created_at', 'desc')
                              ->take(4)
                              ->get();
        
        return view('home', compact('featuredProducts', 'featuredPetshops'));
    }
}