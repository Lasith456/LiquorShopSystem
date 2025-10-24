<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference_no', 'date', 'total_value', 'user_id'
    ];
     protected $casts = [
        'date' => 'datetime',
    ];
    public function items()
    {
        return $this->hasMany(StockItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
