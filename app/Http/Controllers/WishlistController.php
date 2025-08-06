<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $wishlistItems = auth()->user()->wishlists()->with('product')->paginate(12);
        
        return view('wishlist.index', compact('wishlistItems'));
    }

    public function store(Product $product)
    {
        // Verificar se já não está na wishlist
        $exists = Wishlist::where('user_id', auth()->id())
                         ->where('product_id', $product->id)
                         ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'Produto já está na sua lista de desejos!'
            ], 409);
        }

        Wishlist::create([
            'user_id' => auth()->id(),
            'product_id' => $product->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Produto adicionado à lista de desejos!',
            'wishlist_count' => auth()->user()->wishlists()->count()
        ]);
    }

    public function destroy(Product $product)
    {
        $wishlist = Wishlist::where('user_id', auth()->id())
                           ->where('product_id', $product->id)
                           ->first();

        if (!$wishlist) {
            return response()->json([
                'success' => false,
                'message' => 'Produto não encontrado na sua lista de desejos!'
            ], 404);
        }

        $wishlist->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produto removido da lista de desejos!',
            'wishlist_count' => auth()->user()->wishlists()->count()
        ]);
    }

    public function toggle(Product $product)
    {
        $wishlist = Wishlist::where('user_id', auth()->id())
                           ->where('product_id', $product->id)
                           ->first();

        if ($wishlist) {
            // Se existe, remove
            $wishlist->delete();
            $message = 'Produto removido da lista de desejos!';
            $isInWishlist = false;
        } else {
            // Se não existe, adiciona
            Wishlist::create([
                'user_id' => auth()->id(),
                'product_id' => $product->id,
            ]);
            $message = 'Produto adicionado à lista de desejos!';
            $isInWishlist = true;
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'is_in_wishlist' => $isInWishlist,
            'wishlist_count' => auth()->user()->wishlists()->count()
        ]);
    }
}