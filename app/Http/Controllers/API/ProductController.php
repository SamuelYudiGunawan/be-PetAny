<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Order;
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
            'image' => 'required|file|mimes:png,jpg,jpeg|max:1024',
            'stock' => 'required|string',
            'price' => 'required|string',
        ]);

        try {

        if ($request->hasFile('image')) {
            $imageName = Carbon::now()->format('YmdHis') . "_" . md5_file($request->file('image')) . "." . $request->file('image')->getClientOriginalExtension();
            $imagePath = "storage/document/petshop_image/" . $imageName;
            $request->image->storeAs(
                "public/document/petshop_image",
                $imageName
            );
            $image = url('/').'/'.$imagePath;
        }
        $user = Auth::user();
        $product = Product::create([
            'petshop_id' => $request->petshop_id,
            'name' => $request->name,
            'description' => $request->description,
            'image' => $image,
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
        try {
            $data = Product::where('stock', '>=', 1)->get();
            $response = [];
            foreach ($data as $d) {
                array_push($response, [
                    'data' => [
                        "name" => $d->name,
                        "description" => $d->description,
                        "image" => $d->image,
                        "stock" => $d->stock,
                        "price" => number_format($d->price, 0, ',', '.'),
                    ],
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
            $d = Product::where('id', $id)->with('petshop_id')->first();
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
        'image' => 'file|mimes:png,jpg,jpeg|max:1024',
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

    public function getAllUserProductTransaction(){
        try{
            $data = Order::where('user_id', Auth::user()->id)->get();
            $response = [];
            foreach($data as $d) {
                $orderCollection = Order::where('order_id', $d->order_id)->get();
                $orderArray = [];
                foreach ($orderCollection as $order) {
                    array_push($orderArray, [
                        'order_id' => $order->order_id,
                        'amount' => "Rp " . number_format($order->gross_amount, 0, ',', '.'),
                        'type' => $order->type,
                        'time' => $order->updated_at->format('H:i'),
                        'date' => $order->date,
                        'quantity' => $order->quantity,
                        'status' => $order->transaction_status,
                    ]);
                if ($order->transaction_status === 'settlement') {
                    $productCollection = Product::where('id', $d->product_id)->get();
                    $productArray = [];
                    foreach ($productCollection as $product) {
                        array_push($productArray, [
                            'name' => $product->name,
                            'image' => $product->image,
                        ]);
                        $petshops = User::where('id', $product->petshop_id)->first();
                    $petshopCollection = Petshop::where('id', $petshops->petshop_id)->get();
                    $petshopArray = [];
                    foreach ($petshopCollection as $petshop) {
                        array_push($petshopArray, [
                            'petshop_name' => $petshop->petshop_name,
                        ]);
                    }
                    array_push($response, [
                        'name' => $product->name,
                        'image' => $product->image,
                        'quantity' => $d->quantity,
                        'amount' => "Rp " . number_format($d->gross_amount, 0, ',', '.'),
                        'orders' => $orderArray,
                        'petshop' => $petshopArray,
                        'product' => $productArray,
                        'links' => 'product-transaction/' . $d->order_id,
                    ]);
                }
            }
        }
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
