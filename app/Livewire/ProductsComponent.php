<?php

namespace App\Livewire;

use App\Models\Categories;
use App\Models\Products;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProductsComponent extends Component
{
    public $products;
    public $categories;
    public $searchTerm;
    public $category_id;
    public $min_price;
    public $max_price;

    public function mount()
    {
        $this->categories = Categories::all();
        $this->products = Products::with('vendor')->get();
    }
    public function updated($propertyName)
    {
        $this->filterProducts();
    }
    public function filterProducts()
    {
        $query = Products::query();

        if ($this->searchTerm) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        }

        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        if ($this->min_price || $this->max_price) {
            $query->where(function ($query) {
                if ($this->min_price) {
                    $query->whereRaw('price - (price * discount / 100) >= ?', [$this->min_price]);
                }
                if ($this->max_price) {
                    $query->whereRaw('price - (price * discount / 100) <= ?', [$this->max_price]);
                }
            });
        }

        $this->products = $query->with('vendor')->get();
    }

    public function addToCart($productId)
    {
        $product = Products::findOrFail($productId);

        if ($product->stock > 0) {
            Cart::add($product->id, $product->name, 1, $product->price, ['description' => $product->description]);
            session()->flash('success', 'Product added to cart');
        } else {
            session()->flash('error', 'Product is out of stock');
        }
    }
    public function addToWishlist($productId)
    {
        $user = Auth::user();
        $wishlist = Wishlist::firstOrCreate(['user_id' => $user->id]);
        $wishlistItem = WishlistItem::where('wishlist_id', $wishlist->id)->where('product_id', $productId)->first();

        if (!$wishlistItem) {
            WishlistItem::create([
                'wishlist_id' => $wishlist->id,
                'product_id' => $productId,
            ]);
            session()->flash('success', 'Product added to wishlist');
        } else {
            session()->flash('error', 'Product is already in your wishlist');
        }
    }
    public function render()
    {
        $user = Auth::user();
        $wishlistItems = WishlistItem::whereHas('wishlist', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('product')->get();
        return view('livewire.products-component', [
            'products' => $this->products,
            'categories' => $this->categories,
            'cart' => Cart::content(),
            'wishlistItems' => $wishlistItems,
        ]);
    }
}
