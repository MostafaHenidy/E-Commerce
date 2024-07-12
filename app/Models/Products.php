<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;
    protected $fillable =[
        'name',
        'description',
        'price',
        'stock',
        'vendor_id',
        'category_id',
        'image',
    ];
    public function vendor(){
        return $this->belongsTo(Vendor::class,'vendor_id');
    }
    public function category(){
        return $this->belongsTo(Categories::class);
    }
    public function review(){
        return $this->hasMany(Review::class);
    }
    public function orderItem(){
        return $this->hasmany(OrderItem::class);
    }
}
