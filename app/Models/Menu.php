<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'restaurant_id',
        'item_name',
        'item_description',
        'price',
        'is_available',
        'image',
        'category_id'
    ];

    // 🔹 A menu item belongs to a restaurant
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    // app/Models/Menu.php

    public function category()
    {
        // If your foreign key is 'category_id', Laravel finds it automatically
        return $this->belongsTo(Category::class);
    }
}
