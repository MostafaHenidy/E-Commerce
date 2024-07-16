<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Products extends Model
{
    use HasFactory, Searchable;
    protected $fillable = [
        'name',
        'description',
        'price',
        'discount',
        'discount_start_date',
        'discount_end_date',
        'vendor_id',
        'category_id',
        'stock',
        'image',
        'sizes',
        'colors'
    ];
    public function vendor()
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }
    public function category()
    {
        return $this->belongsTo(Categories::class);
    }
    public function review()
    {
        return $this->hasMany(Review::class);
    }
    public function orderItem()
    {
        return $this->hasmany(OrderItem::class);
    }
    public function productImage()
    {
        return $this->hasMany(ProductImage::class);
    }
    public function wishlistItem()
    {
        return $this->hasMany(WishlistItem::class);
    }
    public function getDiscountedPriceAttribute()
    {
        if ($this->discount > 0 && now()->between($this->discount_start_date, $this->discount_end_date)) {
            return $this->price - ($this->price * ($this->discount / 100));
        }
        return $this->price;
    }
    public function toSearchableArray(): array
    {
        // All model attributes are made searchable
        return [
            'name' => $this->name,
            'description' => $this->description,
        ];
    }
}
