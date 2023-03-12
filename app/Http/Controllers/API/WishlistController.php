<?php

namespace App\Http\Controllers\API;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function addWishlist(Request $request)
    {
        $userId = Auth::user()->id;
        $productId = $request->product_id;
    
        // Check if a Wishlist record with the given product_id already exists for the current user
        $wishlist = Wishlist::where('user_id', $userId)->where('product_id', $productId)->first();
    
        if ($wishlist) {
            // Wishlist record already exists, return an error response
            return response()->json(['error' => 'Product already in wishlist'], 400);
        } else {
            // Wishlist record does not exist, create a new one
            $wishlist = Wishlist::create([
                'user_id' => $userId,
                'product_id' => $productId
            ]);
    
            return response()->json($wishlist);
        }
    }    

    public function getWishlist(Request $request){
        $wishlists = Wishlist::where('user_id', Auth::user()->id)->with(['user_id', 'product_id'])->get();
        $response = [];
        foreach($wishlists as $wishlist){
            // dd($wishlist->product_id);
            array_push($response, [
                'data' => $wishlist,
                'links' => '/product/' . $wishlist->product_id,
            ]);
        }
        return response()->json($response);
    }

    public function removeWishlist(Request $request)
    {
        $userId = Auth::user()->id;
        $productId = $request->product_id;

        // Find the Wishlist record with the given product_id for the current user
        $wishlist = Wishlist::where('user_id', $userId)->where('product_id', $productId)->first();

        // dd($wishlist);

        if ($wishlist) {
            // Wishlist record found, delete it
            $wishlist->delete();

            return response()->json(['message' => 'Product removed from wishlist']);
        } else {
            // Wishlist record not found, return an error response
            return response()->json(['error' => 'Product not found in wishlist'], 400);
        }

    }

}
