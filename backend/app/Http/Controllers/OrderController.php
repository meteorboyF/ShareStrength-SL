<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user instanceof User) {
            return response()->json(['message' => 'Only PWD users can place orders'], 403);
        }

        $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_details' => 'required',
            'shipping_address' => 'required',
        ]);

        return DB::transaction(function () use ($request) {
            $totalAmount = 0;
            $itemsToCreate = [];

            // Calculate total and prepare items
            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['id']);
                
                if ($product->stock_quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                $totalAmount += $product->price * $item['quantity'];
                
                $itemsToCreate[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ];

                // Decrement stock
                $product->stock_quantity -= $item['quantity'];
                $product->save();
            }

            // Create Order
            $order = Order::create([
                'user_id' => $user->getKey(),
                'total_amount' => $totalAmount,
                'status' => 'paid', // Assume clean payment for now
                'payment_details' => $request->payment_details,
                'shipping_address' => $request->shipping_address,
            ]);

            // Create Order Items
            foreach ($itemsToCreate as $itemData) {
                $order->items()->create($itemData);
            }

            return response()->json(['message' => 'Order created successfully', 'order_id' => $order->id], 201);
        });
    }
}
