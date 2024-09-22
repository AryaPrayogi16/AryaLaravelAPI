<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        
        return response()->json([
            'message' => 'Product List',
            'data' => $products->isEmpty() ? [] : $products,
        ], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|unique:products,name',
            'price' => 'required|integer|min:1',
            'stock' => 'required|integer|min:0',
        ], [
            'name.required' => 'The name field is required.',
            'price.required' => 'The price field is required.',
            'price.integer' => 'The price field must be a number.',
            'stock.required' => 'The stock field is required.',
            'stock.integer' => 'The stock field must be a number.',
        ]);
    
        $product = Product::create([
            'name' => $validatedData['name'],
            'price' => $validatedData['price'],
            'stock' => $validatedData['stock'],
            'sold' => 0, // default value
        ]);
    
        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }
    
    // Menangani error untuk validasi
    protected function invalidJson($request, $validator)
    {
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422);
    }
    
}
