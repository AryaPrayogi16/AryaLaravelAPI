<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('products')->get();

        return response()->json([
            'message' => 'Order List',
            'data' => $orders->isEmpty() ? [] : $orders,
        ], 200);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|integer|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        $orderProducts = [];
        foreach ($validatedData['products'] as $productData) {
            $product = Product::find($productData['id']);
            $quantity = $productData['quantity'];

            // Update stock logic
            if ($product->stock >= $quantity) {
                $product->stock -= $quantity;
                $product->save();
                $orderProducts[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $quantity,
                    'stock' => $product->stock,
                    'sold' => $product->sold,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                ];
            } else {
                return response()->json([
                    'message' => 'Insufficient stock for product ID ' . $productData['id'],
                ], 422);
            }
        }

        // Create the order
        $order = Order::create(); // Assumes you have an order model with proper fillable fields

        return response()->json([
            'message' => 'Order created',
            'data' => [
                'id' => $order->id,
                'products' => $orderProducts,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ], 201);
    }

    public function show($id)
    {
        $order = Order::with('products')->find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        return response()->json([
            'message' => 'Order Detail',
            'data' => $order,
        ], 200);
    }

    public function destroy($id)
    {
        $order = Order::with('products')->find($id);

        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Restore stock logic
        foreach ($order->products as $product) {
            $productModel = Product::find($product->id);
            $productModel->stock += $product->quantity;
            $productModel->save();
        }

        $order->delete();

        return response()->json([
            'message' => 'Order deleted successfully',
            'data' => $order,
        ], 200);
    }
}
