<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\ThirdPartyApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendRecurringOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Order $order;

    // Retry-logiikka
    public $tries = 5;
    public $backoff = 10;

    /**
     * Luo job-instanssi
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Suorita job
     */
    public function handle(ThirdPartyApiService $service)
    {
        try {
            // ===================================================
            // Deduplikointi: ohitetaan jos tilaus on jo käsitelty
            // ===================================================
            if (in_array($this->order->status, ['sent', 'processed', 'high_priority'])) {
                Log::info("Recurring order {$this->order->id} on jo käsitelty, job ohitettu.");
                return;
            }

            // ===================================================
            // Lähetetään tilaus mock-API:lle service-luokan kautta
            // ===================================================
            $result = $service->sendOrder($this->order->toArray());

            if (!$result['success']) {
                Log::warning("Recurring order {$this->order->id} epäonnistui service-kutsussa.", [
                    'response' => $result
                ]);
                // Job jää queueen, retry tapahtuu automaattisesti
                return;
            }

            // ===================================================
            // Status-päivitys ja meta-kentän erityistapaukset
            // ===================================================
            $newStatus = !empty($this->order->meta['priority']) ? 'high_priority' : 'sent';
            $this->order->update(['status' => $newStatus]);

            Log::info("Recurring order {$this->order->id} käsitelty onnistuneesti, status: {$newStatus}.");

        } catch (\Exception $e) {
            $this->order->update(['status' => 'failed']);
            Log::error("Recurring order {$this->order->id} poikkeus: {$e->getMessage()}");
            throw $e; // retry queue:n mukaan
        }
    }
}
