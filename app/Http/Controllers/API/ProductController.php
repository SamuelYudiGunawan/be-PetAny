<?php

namespace App\Http\Controllers\API;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

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
        } catch (\Exception $e) {
        Log::error($e->getMessage());
        }

        return response()->json([
            'data' => $product,
        ]);
    
    }
    public function getAllProduct(){
        try{
            $data = Product::with('user_id:id,name')->get();
        } catch (\Exception $e) {
        Log::error($e->getMessage());
        }
        
        return response()->json([
            'data' => $data,
        ]);
    }

    public function getProduct($id)
    {
        try{
            $data = Product::with('user_id:id,name')->find($id);
        } catch (\Exception $e) {
        Log::error($e->getMessage());
        }
        
        return response()->json([
            'data' => $data,
        ]);
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
