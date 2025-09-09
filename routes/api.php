<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Subscription;
use App\Jobs\ProcessSubscriptionOrder;
use App\Jobs\SendRecurringOrder;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Näitä reittejä käytetään testaukseen ja kehitykseen.
| Kaikki vastaukset palautetaan aina JSON-muodossa.
|
*/

// =======================================================
// POST: vastaanottaa tilauksen
// =======================================================
Route::post('/submit-order', function (Request $request) {

    try {
        $validated = $request->validate([
            'order' => 'required|string|max:255',
            'recurring' => 'sometimes|boolean',
            'meta' => 'nullable|array',
        ]);

        $orderName = $validated['order'];
        $recurring = $validated['recurring'] ?? false;
        $meta = $validated['meta'] ?? null;

        $order = Order::firstOrCreate(
            ['order_name' => $orderName],
            ['recurring' => $recurring, 'meta' => $meta, 'status' => 'pending']
        );

        if (!empty($meta['priority'])) {
            $order->status = 'high_priority';
            $order->save();
        }

        // ===================================================
        // Dispatchataan job queueen
        // ===================================================
        SendRecurringOrder::dispatch($order);

        return response()->json([
            'success' => true,
            'message' => 'Tilaus vastaanotettu ja job dispatchattu',
            'data' => $order->toArray(),
        ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validointivirhe',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        Log::error('Virhe tilauksen tallennuksessa', [
            'order_name' => $request->input('order'),
            'meta' => $request->input('meta'),
            'exception' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Tapahtui virhe tilauksen tallennuksessa',
        ], 500);
    }
});

// =======================================================
// POST: vastaanottaa subscriptionin
// =======================================================
Route::post('/submit-subscription', function (Request $request) {

    try {
        $validated = $request->validate([
            'subscription' => 'required|string|max:255',
            'recurring' => 'sometimes|boolean',
            'meta' => 'nullable|array',
        ]);

        $subscriptionName = $validated['subscription'];
        $recurring = $validated['recurring'] ?? false;
        $meta = $validated['meta'] ?? null;

        $subscription = Subscription::firstOrCreate(
            ['subscription_name' => $subscriptionName],
            ['recurring' => $recurring, 'meta' => $meta, 'status' => 'active']
        );

        if (!empty($meta['priority'])) {
            $subscription->status = 'high_priority';
            $subscription->save();
        }

        // ===================================================
        // Dispatchataan subscription-job queueen
        // ===================================================
        ProcessSubscriptionOrder::dispatch($subscription);

        return response()->json([
            'success' => true,
            'message' => 'Subscription vastaanotettu ja job dispatchattu',
            'data' => $subscription->toArray(),
        ], 200);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validointivirhe',
            'errors' => $e->errors()
        ], 422);

    } catch (\Exception $e) {
        Log::error('Virhe subscriptionin tallennuksessa', [
            'subscription_name' => $request->input('subscription'),
            'meta' => $request->input('meta'),
            'exception' => $e->getMessage()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Tapahtui virhe subscriptionin tallennuksessa',
        ], 500);
    }
});
