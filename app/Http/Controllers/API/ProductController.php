<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function addProduct(Request $request){
        $request->validate([
            'petshop_id' => 'required|string',
            'name' => 'required|string',
            'description' => 'required|string',
            'image' => 'required|file|mimes:png,jpg',
            'stock' => 'required|string',
            'price' => 'required|string',
            'location' => 'required|string',
            'category' => 'required|string',
        ]);

        try {

        $imageName = Carbon::now()->format('YmdHis') . "_" . md5_file($request->file('image')) . "." . $request->file('image')->getClientOriginalExtension();
        $imagePath = "storage/document/product_image/" . $imageName;
        $request->image->storeAs(
            "public/document/product_image",
            $imageName
        );

        $product = Product::create([
            'petshop_id' => $request->petshop_id,
            'name' => $request->name,
            'description' => $request->description,
            'image' => url('/').'/'.$imagePath,
            'stock' => $request->stock,
            'price' => $request->price,
            'location' => $request->location,
            'category' => $request->category,
        ]);
        return response()->json([
            'data' => $product,
        ]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }
    public function getAllProduct(){
        try{
            $data = Product::with('petshop_id:id,id')->get();
            $response = [];
            foreach ($data as $d) {
                array_push($response, [
                    'petshop_id' => $d->petshop_id,
                    'name' => $d->name,
                    'description' => $d->description,
                    'image' => $d->image,
                    'stock' => $d->stock,
                    'price' => $d->price,
                    'location' => $d->location,
                    'category' => $d->category,
                    'links' => [
                        'self' => '/api/get-product/' . $d->id,
                    ],
                ]);
            }
            return response()->json([
                $response
            ]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
        
    }
    public function getProduct($id)
    {
        try{
            $d = Product::with('petshop_id:id')->find($id)->first();
            return response()->json([
                'petshop_id' => $d->petshop_id,
                'name' => $d->name,
                'description' => $d->description,
                'image' => $d->image,
                'stock' => $d->stock,
                'price' => $d->price,
                'location' => $d->location,
                'category' => $d->category,
                'links' => [
                    'add_wishlist' => 'api/add-wishlist?product_id=' . $d->id,
                    'add_cart' => 'api/add-cart?product_id=' . $d->id,
                ],
            ]);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }

    public function editProduct(Request $request, $id) {
    $request->validate([
        'name' => 'string',
        'description' => 'string',
        'image' => 'file|mimes:png,jpg',
        'price' => 'string',
        'category' => 'string',
    ]);

    $product = Product::find($id);
    if (!$product) {
        return response()->json(['error' => 'Product not found.'], 404);
    }

    try {
        if ($request->hasFile('image')) {
            $imageName = Carbon::now()->format('YmdHis') . "_" . md5_file($request->file('image')) . "." . $request->file('image')->getClientOriginalExtension();
            $imagePath = "storage/document/product_image/" . $imageName;
            $request->image->storeAs(
                "public/document/product_image",
                $imageName
            );
            $product->image = url('/').'/'.$imagePath;
        }

        $product::where('id', $id)->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category' => $request->category,
            'image' => $request->hasFile('image') ? url('/').'/'.$imagePath : $product->image,
        ]);

        return response()->json([
            'message' => 'Product updated',
        ]);
    } catch (\Exception $e) {
        $errorMessage = $e->getMessage();
        Log::error($errorMessage);
        return response()->json([
            'error' => $errorMessage
        ], 500);
    }
}

    public function deleteProduct($id) {
        try{
        $product = Product::where('id', $id)->first();
        if(!$product) { 
            return response()->json(['message' => 'Product not found']); 
        }
        $product->delete();
        return response()->json(['message' => 'Product deleted']); 
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }

    public function getProductForm()
    {
        return [
            [
                'name' => 'name',
                'type' => 'text',
                'label' => 'Nama Produk',
                'required' => true,
            ],
            [
                'name' => 'category',
                'type' => 'dropdown',
                'label' => 'Kategori Produk',
                'required' => true,
            ],
            [
                'name' => 'description',
                'type' => 'text',
                'label' => 'Deskripsi Porduk',
                'required' => true,
            ],
            [
                'name' => 'price',
                'type' => 'text',
                'label' => 'Harga',
                'required' => true,
            ],
            [
                'name' => 'stock',
                'type' => 'number ',
                'label' => 'Stock',
                'required' => true,
            ],
            [
                'name' => 'image',
                'type' => 'file',
                'label' => 'Foto Produk',
                'required' => true,
            ],
        ];
    }

}
