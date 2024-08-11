<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_name',
        'item_description',
        'price',
        'category_id',
        'discount_id'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
