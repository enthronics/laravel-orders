<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    // Määritellään selvästi taulun nimi
    protected $table = 'subscriptions';

    // Sallitut kentät mass assignmentille
    protected $fillable = [
        'subscription_name',
        'recurring',
        'meta',
        'status',
    ];

    // Kenttien tyyppimuunnokset
    protected $casts = [
        'recurring' => 'boolean', // checkbox palauttaa aina true/false
        'meta' => 'array',        // JSON-kenttä muuntuu automaattisesti taulukoksi
    ];
}
