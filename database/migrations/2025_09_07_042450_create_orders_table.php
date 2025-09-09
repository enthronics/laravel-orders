<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Orders-taulun luonti
 *
 * Tämä taulu tallentaa tilaukset.
 * Kentät: order_name, recurring, meta, status, timestampit.
 */
return new class extends Migration
{
    /**
     * Suorita migraatio (luo taulu).
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            $table->string('order_name');     // Tilauksen nimi
            $table->boolean('recurring')->default(false); // Onko toistuva tilaus
            $table->json('meta')->nullable(); // Lisätietoja JSON-formaatissa
            $table->string('status')->default('pending'); // Tilausstatus

            $table->timestamps(); // created_at ja updated_at
        });
    }

    /**
     * Peru migraatio (poista taulu).
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
