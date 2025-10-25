<?php
  
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'detail', 'category_id', 'qty', 'selling_price'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // 🔥 Many-to-Many Relationship
    public function sizes()
    {
        return $this->belongsToMany(Size::class, 'product_size')
            ->withPivot('qty', 'selling_price')
            ->withTimestamps();
    }


}
