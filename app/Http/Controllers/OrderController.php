<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Dish;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function store()
    {
        $dish = Dish::inRandomOrder()->first();

        $order = new Order();
        $order->dish_id = $dish->id;
        $order->save();

        $this->request_ingredients($order);

        return response()->json(['message' => 'Orden registrada exitosamente'], 201);
    }

    public function pending_orders()
    {
        $pending_orders = Order::with('dish', 'dish.ingredients')->where('status', 'pending')->orderBy('id', 'desc')->get();

        return response()->json([
            'data' => $pending_orders
        ]);
    }
    public function finished_orders()
    {
        $pending_orders = Order::with('dish', 'dish.ingredients')->where('status', 'finished')->orderBy('id', 'desc')->get();

        return response()->json([
            'data' => $pending_orders
        ]);
    }

    public function get_ingredients(Request $request)
    {
        Log::info("Ingredients received");
        $order = Order::where('id', $request->order_code)->first();
        $order->status = "finished";
        $order->save();
        Log::info("Order status changed to 'finished'");
    }

    private function request_ingredients($order)
    {
        $formatted_ingredients = [];

        foreach ($order->dish->ingredients as $ingredient) {
            $formatted_ingredients[] = [
                'name' => $ingredient['name'],
                'quantity' => $ingredient['pivot']['quantity'],
            ];
        }

        $body = [
            'order_code' => $order->id,
            'ingredients' => $formatted_ingredients,
        ];

        $url = env('API_BODEGA_URL') . '/api/orders/create';

        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->post($url, $body);

        Log::info($response->json());
    }
}
