<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart_items extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'item_id',
        'qty',
        'note',
        'price'
    ];
}
