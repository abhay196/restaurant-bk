<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Restaurant extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'address',
        'phone',
        'description',
        'type',
        'is_available',
        'image',
    ];

    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$value) return null;

                // 1. If the string already starts with http, it's a full Cloudinary URL.
                // Return it exactly as it is in the database.
                if (str_starts_with($value, 'http')) {
                    return $value;
                }

                // 2. ONLY if it's a partial path (like 'restaurant/abc.jpg'), 
                // then we build the Cloudinary URL.
                $cloudName = 'dkwsaccn9'; 
                return "https://res.cloudinary.com/{$cloudName}/image/upload/v1/{$value}";
            },
        );
    }
}
