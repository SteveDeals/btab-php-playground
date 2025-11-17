<?php

namespace Btab\Listeners;

use Btab\Http\BtabClient;
use Btab\Jobs\ImportProductsJob;
use Illuminate\Support\Facades\Log;

class SendPurchaseToBtab
{
    public function handle($event)
    {
        try {
            $client = new BtabClient();
            $payload = [
                'order_id' => $event->order->id,
                'total' => $event->order->price,
                'currency' => $event->order->currencyid,
                'items' => $event->order->products->map(function ($item) {
                    return [
                        'external_id' => $item->product->code,
                        'qty' => $item->quantity,
                        'price' => $item->price,
                    ];
                })->toArray(),
            ];

            $res = $client->sendPurchase($payload);
            if ($res->successful()) {
                dispatch(new ImportProductsJob());
                Log::info("Purchase sent successfully to BTAB");
            } else {
                Log::error("Failed to send purchase to BTAB: " . $res->body());
            }
        } catch (\Throwable $e) {
            Log::error("BTAB purchase sync error", ['error' => $e->getMessage()]);
        }
    }
}
