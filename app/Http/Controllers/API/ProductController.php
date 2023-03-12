<?php

namespace App\Http\Controllers\API;

use App\Models\Petshop;
use App\Models\Product;
use Illuminate\Support\Str;
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
        ]);

        try {

        if ($request->hasFile('image')) {
            $imageName = Carbon::now()->format('YmdHis') . "_" . md5_file($request->file('image')) . "." . $request->file('image')->getClientOriginalExtension();
            $imagePath = "storage/document/product_image/" . $imageName;
            $request->image->storeAs(
                "public/document/product_image",
                $imageName
            );
            $product_image = url('/').'/'.$imagePath;
            $petshop->image = $product_image;
            $petshop->save();
        }
        $user = Auth::user();
        $product = Product::create([
            'petshop_id' => $user->petshop_id,
            'name' => $request->name,
            'description' => $request->description,
            // 'image' => url('/').'/'.$imagePath,
            'stock' => $request->stock,
            'price' => $request->price,
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
            $data = Product::where('stock', '>=', 1)->with('petshop_id')->get();
            $response = [];
            foreach ($data as $d) {
            $petshop = Petshop::where('id', $d->petshop_id)->first();
            $petshop_name = Str::slug($petshop->petshop_name);
            $product_name = Str::slug($d->name);
                array_push($response, [
                    'data' => $d,
                    'links' => '/' . $petshop_name . '/' . $product_name,
                ]);
            }
            return response()->json($response);
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            Log::error($errorMessage);
            return response()->json([
                'error' => $errorMessage
            ], 500);
        }
    }

    public function getPetshopProduct(){
        try{
            $data = Product::where('petshop_id', Auth::user()->petshop_id)->get();
            $response = [];
            foreach ($data as $d) {
                array_push($response, [
                    'data' => $d,
                    'links' => '/api/get-product/' . $d->id,
                ]);
            }
            return response()->json($response);
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
            $d = Product::with('petshop_id:id')->find($id)->with('petshop_id')->first();
            return response()->json([
                'data' => $d,
                'links' => 'api/add-wishlist?product_id=' . $d->id,
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
