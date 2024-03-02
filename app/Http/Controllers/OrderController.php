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

        $response = Http::withHeaders([
            'Accept' => 'application/json',
        ])->post("http://127.0.0.1:8000/api/orders/create", $body);

        Log::info($response->json());
    }
}
