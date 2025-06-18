<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Models\Service;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function storeProductReview(Request $request, Product $product)
    {
        $this->validateReview($request);
        
        // Verificar se o usuário já avaliou este produto
        $existingReview = Review::where('user_id', auth()->id())
                              ->where('reviewable_id', $product->id)
                              ->where('reviewable_type', Product::class)
                              ->first();
        
        if ($existingReview) {
            return back()->with('error', 'Você já avaliou este produto.');
        }
        
        $review = new Review();
        $review->user_id = auth()->id();
        $review->reviewable()->associate($product);
        $review->rating = $request->rating;
        $review->comment = $request->comment;
        $review->save();
        
        return back()->with('success', 'Avaliação enviada com sucesso!');
    }
    
    public function storeServiceReview(Request $request, Service $service)
    {
        $this->validateReview($request);
        
        // Verificar se o usuário já avaliou este serviço
        $existingReview = Review::where('user_id', auth()->id())
                              ->where('reviewable_id', $service->id)
                              ->where('reviewable_type', Service::class)
                              ->first();
        
        if ($existingReview) {
            return back()->with('error', 'Você já avaliou este serviço.');
        }
        
        $review = new Review();
        $review->user_id = auth()->id();
        $review->reviewable()->associate($service);
        $review->rating = $request->rating;
        $review->comment = $request->comment;
        $review->save();
        
        return back()->with('success', 'Avaliação enviada com sucesso!');
    }
    
    private function validateReview(Request $request)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500'
        ]);
    }
}