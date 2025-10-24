<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bottle extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'quantity',
        'price_per_bottle',
        'total_price',
    ];
}
