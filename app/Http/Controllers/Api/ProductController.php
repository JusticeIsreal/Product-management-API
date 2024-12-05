<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    // Retrieve all products.
    public function index(){

        $products = Product::get();
         if( $products->count()>0){
            return ProductResource::collection($products);
         }else{
            return response()->json(["message" => "No product available"], 200);
         }

    }
    // Create a new product.
    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            "name" => "required|string|max:255",
            "description" => "required",
            "price"=>"required|integer",

        ]);

        if($validator->fails()){
            return response()->json(["message" => "All feilds are required","error"=>$validator->errors()], 422);
        };
        $product = Product::create([
            "name" => $request->name,
            "description" => $request->description,
            "price" => $request->price,
        ]);

        return response()->json(["mesage" => "Product created successfully", "data" => new ProductResource($product)], 200);

    }
    // Retrieve a specific product
    public function show( $id){
        $product = Product::find($id);

        if($product){
                return new ProductResource($product);
        }else{
            return response()->json(["message"=> "product not found"],404);
        }
    }
    // Update a specific product
    public function update(Request $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(["message" => "product not found"], 404);
        }

        $validator = Validator::make($request->all(), [
            "name" => "sometimes|string|max:255",
            "description" => "sometimes|max:255",
            "price" => "sometimes|integer",
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => "Inspect input", "error" => $validator->errors()], 422);
        };

        $dataToUpdate = array_filter($request->only(["name", "description", "price"]), function ($val) {
            return $val !== null;
        });

        $product->update($dataToUpdate);
        return response()->json(["message" => "Product updated successfully", "data" => new ProductResource($product)], 200);
    }
    // Delete a specific product.
    public function destroy($id)
    {
        if (!$id) {
            return response()->json(["message" => "Enter complete wquery url"], 422);
        }
        $product = Product::find($id);
        if (!$product) {
            return response()->json(["message" => "Product not found"], 404);
        }
        $product->delete();
        return response()->json(["message" => "Product deleted successfully"], 200);
    }
}
