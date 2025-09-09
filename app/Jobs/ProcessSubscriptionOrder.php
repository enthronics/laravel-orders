<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Services\ThirdPartyApiService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessSubscriptionOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Subscription $subscription;

    // Retry-logiikka: max 5 kertaa, 10 sekunnin välein
    public $tries = 5;
    public $backoff = 10;

    /**
     * Luo uusi job-instanssi
     */
    public function __construct(Subscription $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Suorita job
     */
    public function handle(ThirdPartyApiService $service)
    {
        try {
            // Varmistetaan, että käsittely koskee vain toistuvia tilauksia
            if (!$this->subscription->recurring) {
                Log::info("Subscription {$this->subscription->id} ei ole toistuva, job ohitettu.");
                return;
            }

            // Deduplikointi: tarkistetaan, ettei subscription ole jo käsitelty
            if (in_array($this->subscription->status, ['processed', 'sent'])) {
                Log::info("Subscription {$this->subscription->id} on jo käsitelty, job ohitettu.");
                return;
            }

            // Lähetetään subscription kolmannen osapuolen palveluun
            $result = $service->sendOrder($this->subscription->toArray());

            if (!$result['success']) {
                Log::warning("Toistuvan subscriptionin {$this->subscription->id} käsittely epäonnistui.", [
                    'response' => $result
                ]);
                return; // Job retry queue:n mukaan
            }

            // Päivitetään status ja käsitellään meta-kentän erityistapaukset
            $newStatus = !empty($this->subscription->meta['priority']) ? 'high_priority' : 'processed';
            $this->subscription->update(['status' => $newStatus]);

            Log::info("Toistuva subscription {$this->subscription->id} käsitelty onnistuneesti, status: {$newStatus}.");

        } catch (\Exception $e) {
            Log::error("Job ProcessSubscriptionOrder poikkeus subscriptionille {$this->subscription->id}", [
                'exception' => $e->getMessage(),
                'subscription' => $this->subscription->toArray()
            ]);
            throw $e; // retry queue:n mukaan
        }
    }
}
