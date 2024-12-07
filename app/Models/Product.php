<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;
    protected $guarded =[];

    public function productImage(){
        return $this->hasMany(ProductImage::class);
    }
    public function reviews(){
        return $this->hasMany(Review::class);
    }
    public function orederItems(){
        return $this->hasMany(OrderItem::class);
    }
    public function cart(){
        return $this->hasMany(Cart::class);
    }
    public function subCategory(){
        return $this->belongsTo(Subcategory::class);
    }
}
