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
        Log::error($e->getMessage());
        }
    }
    public function getAllProduct(){
        // try{
        //     $data = Product::where('user_id', Auth::user()->id)->get();
        //     return response()->json([
        //         'data' => $data,
        //     ]);
        // } catch (\Exception $e) {
        // Log::error($e->getMessage());
        // }
        

        try{
            // $data = Product::get();
            // $data = Product::where('petshop_id', $data->petshop_id)->get();
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
        Log::error($e->getMessage());
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
        Log::error($e->getMessage());
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

        if ($request->has('name')) {
            $product->name = $request->name;
        }
        if ($request->has('description')) {
            $product->description = $request->description;
        }
        if ($request->has('price')) {
            $product->price = $request->price;
        }
        if ($request->has('category')) {
            $product->category = $request->category;
        }

        $product->save();

        return response()->json([
            'data' => $product,
        ]);
    } catch (\Exception $e) {
        Log::error($e->getMessage());
        return response()->json(['error' => 'Error updating product.'], 500);
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
        Log::error($e->getMessage());
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
