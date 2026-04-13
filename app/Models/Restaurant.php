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

                // 1. If it's already a full URL, just return it
                if (filter_var($value, FILTER_VALIDATE_URL)) {
                    return $value;
                }

                // 2. If it's a local path, point it to Cloudinary
                // Replace 'your_cloud_name' with your actual Cloudinary name
                $cloudName = 'dkwsaccn9'; 
                return "https://res.cloudinary.com/{$cloudName}/image/upload/v1/{$value}";
            },
        );
    }
}
