<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'item_qty',
        'total_price',
        'status',
        'paymemt_method',
        'note',
    ]; 

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
