<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Jobs\ProcessSubscriptionOrder;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->json()->all();

        $orderName = $data['order'] ?? null;
        $recurring = $data['recurring'] ?? false;
        $meta = $data['meta'] ?? null;

        if (!$orderName) {
            return response()->json([
                'success' => false,
                'message' => 'Tilaus tarvitsee nimen (order_name)!'
            ], 400);
        }

        $order = Order::create([
            'order_name' => $orderName,
            'recurring' => $recurring,
            'meta' => $meta,
            'status' => 'pending'
        ]);

        // Jos tilaus on toistuva, laitetaan työnjono käsittelemään
        if ($recurring) {
            ProcessSubscriptionOrder::dispatch($order);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tilaus tallennettu onnistuneesti',
            'data' => $order
        ]);
    }
}
