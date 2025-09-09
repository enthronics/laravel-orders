<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Order;

class ExampleTest extends TestCase
{
    use RefreshDatabase; // Tyhjentää tietokannan jokaisen testin alussa

    /**
     * Testaa, että etusivu palauttaa 200 OK.
     */
    public function test_homepage_returns_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Testaa POST /submit-order onnistuneesti.
     */
    public function test_submit_order_creates_order(): void
    {
        $payload = [
            'order' => 'Testituote',
            'recurring' => true,
            'meta' => ['priority' => 'high']
        ];

        $response = $this->postJson('/api/submit-order', $payload);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Tilaus vastaanotettu ja tallennettu'
                 ]);

        $this->assertDatabaseHas('orders', [
            'order_name' => 'Testituote',
            'recurring' => true,
            'status' => 'high_priority'
        ]);
    }

    /**
     * Testaa validoinnin: order_name puuttuu.
     */
    public function test_submit_order_requires_order_name(): void
    {
        $payload = [
            'recurring' => true,
            'meta' => ['priority' => 'low']
        ];

        $response = $this->postJson('/api/submit-order', $payload);

        $response->assertStatus(422) // Laravel validointi palauttaa 422
                 ->assertJsonValidationErrors(['order']);
    }

    /**
     * Testaa deduplikoinnin: identtinen tilaus ei luo uutta.
     */
    public function test_submit_order_prevents_duplicate(): void
    {
        $payload = [
            'order' => 'DupliTesti',
            'recurring' => false,
            'meta' => ['priority' => 'normal']
        ];

        // Ensimmäinen tilaus
        $this->postJson('/api/submit-order', $payload);

        // Toinen identtinen tilaus
        $this->postJson('/api/submit-order', $payload);

        // Varmistetaan, että tietokannassa on vain yksi rivi
        $this->assertEquals(1, Order::where('order_name', 'DupliTesti')->count());
    }
}
