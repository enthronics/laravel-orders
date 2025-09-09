<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Order-malli
 *
 * Tämä vastaa "orders"-tietokantataulua.
 * Taulu sisältää tiedot asiakkaiden tilauksista.
 */
class Order extends Model
{
    use HasFactory;

    // Sallitut kentät, jotka voidaan täyttää massana (mass assignment)
    protected $fillable = [
        'order_name',
        'recurring',
        'meta',
        'status',
    ];

    // Jos meta halutaan aina JSON:ina, voidaan käyttää cast-määrittelyä
    protected $casts = [
        'recurring' => 'boolean',
        'meta' => 'array',
    ];
}
