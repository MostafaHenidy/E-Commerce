<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductImage;
use App\Models\Products;
use App\Models\Review;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
    }
    // ------------------------------------------------------------Product Management
    public function indexProducts(Request $request)
    {
        $query = Products::query();

        // Filter by category
        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        // Filter by price after discount
        if (($request->has('min_price') && $request->min_price != '') || ($request->has('max_price') && $request->max_price != '')) {
            $query->where(function ($query) use ($request) {
                if ($request->has('min_price') && $request->min_price != '') {
                    $query->whereRaw('price - (price * discount / 100) >= ?', [$request->min_price]);
                }
                if ($request->has('max_price') && $request->max_price != '') {
                    $query->whereRaw('price - (price * discount / 100) <= ?', [$request->max_price]);
                }
            });
        }

        // Fetch products with vendor relationships
        $products = $query->with('vendor')->get();

        $categories = Categories::all();
        $cart = Cart::content();
        $user = Auth::user();
        $wishlistItems = WishlistItem::whereHas('wishlist', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('product')->get();
        return view('user.products.index', compact('products', 'cart', 'categories', 'wishlistItems'));
    }

    public function search(Request $request)
    {
        $searchTerm = $request->input('search');
        $products = Products::search($searchTerm)->get();
        $categories = Categories::all();
        $cart = Cart::content();

        if ($products->isEmpty()) {
            return redirect()->route('user.products.index')->with('status', 'No products found');
        }
        $user = Auth::user();
        $wishlistItems = WishlistItem::whereHas('wishlist', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('product')->get();

        return view('user.products.index', compact('products', 'cart', 'categories', 'wishlistItems'));
    }



    public function showProduct($id)
    {
        $product = Products::find($id);
        $reviews = Review::where('product_id', $id)->with('user')->get();
        $averageRating = $reviews->avg('rating');
        $productImages = ProductImage::where('product_id', $id)->get();
        return view('user.products.show', compact('product', 'reviews', 'averageRating', 'productImages'));
    }

    // ------------------------------------------------------------Wishlist Management
    public function indexWishlist()
    {
        $user = Auth::user();
        $wishlistItems = WishlistItem::whereHas('wishlist', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('product')->get();

        return view('user.wishlist.index', compact('wishlistItems'));
    }

    public function addToWishlist($productId)
    {
        $user = Auth::user();

        // Retrieve or create the wishlist
        $wishlist = Wishlist::firstOrCreate(['user_id' => $user->id]);

        // Check if the product already exists in the wishlist
        $wishlistItem = WishlistItem::where('wishlist_id', $wishlist->id)
            ->where('product_id', $productId)
            ->first();
        if ($wishlistItem) {
            return redirect()->back()->with('error', 'Product is already in your wishlist');
        }
        // If the product does not exist in the wishlist, add it
        if (!$wishlistItem) {
            WishlistItem::create([
                'wishlist_id' => $wishlist->id,
                'product_id' => $productId,
            ]);
        }

        return redirect()->back()->with('success', 'Product added to wishlist');
    }


    public function removeFromWishlist($productId)
    {
        $user = Auth::user();

        // Retrieve the wishlist for the authenticated user
        $wishlist = Wishlist::where('user_id', $user->id)->first();

        // If a wishlist exists, find the wishlist item and remove it
        if ($wishlist) {
            $wishlistItem = WishlistItem::where('wishlist_id', $wishlist->id)
                ->where('product_id', $productId)
                ->first();

            if ($wishlistItem) {
                $wishlistItem->delete();
                return redirect()->back()->with('success', 'Product removed from wishlist');
            }
        }

        return redirect()->back()->with('error', 'Product not found in wishlist');
    }

    // ------------------------------------------------------------Order Management
    public function createOrder()
    {
        $cart = Cart::content();
        if ($cart->isEmpty()) {
            return redirect()->route('user.products.index')->with('info', 'Your cart is empty.');
        }
        return view('user.orders.create', compact('cart'));
    }
    public function storeOrder(Request $request)
    {
        $cartContent = Cart::content();
        $totalAmount = Cart::subtotal();

        // Create a new order
        $order = new Order();
        $order->user_id = auth()->user()->id;
        $order->total_amount = $totalAmount;
        $order->status = 'pending'; // You can set the initial status as 'pending' or any other status you prefer
        $order->save();

        // Iterate over cart items and create order items
        foreach ($cartContent as $item) {
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $item->id;
            $orderItem->quantity = $item->qty;
            $orderItem->price = $item->price;
            $orderItem->save();
            $product = Products::find($item->id);
            if ($product) {
                $product->stock -= $item->qty;
                $product->save();
            }
        }

        // Clear the cart after order is placed
        Cart::destroy();

        return redirect()->route('user.orders.index')->with('success', 'Order placed successfully');
    }

    public function indexOrders()
    {
        $orders = Order::where('user_id', auth()->user()->id)->get();
        return view('user.orders.index', compact('orders'));
    }
    public function viewOrder($id)
    {
        $order = Order::with('orderItems.product')->findOrFail($id);  // Eager load order items
        return view('user.orders.show', compact('order'));
    }
    public function updateOrder(Request $request, $id)
    {
        $order = Order::with('orderItems.product')->findOrFail($id);
        $newStatus = $request->status;
        $currentStatus = $order->status;

        // Check if the new status is different from the current status
        if ($newStatus !== $currentStatus) {
            // If the status is being updated to 'cancelled' and it was not previously 'cancelled', add the stock back
            if ($newStatus === 'cancelled' && $currentStatus !== 'cancelled') {
                foreach ($order->orderItems as $orderItem) {
                    $product = $orderItem->product;
                    if ($product) {
                        $product->stock += $orderItem->quantity;
                        $product->save();
                    }
                }
            }

            // If the status is being updated to 'pending' from 'cancelled', deduct the stock again
            else {
                foreach ($order->orderItems as $orderItem) {
                    $product = $orderItem->product;
                    if ($product && $product->stock >= $orderItem->quantity) {
                        $product->stock -= $orderItem->quantity;
                        $product->save();
                    } else {
                        return redirect()->back()->with('error', 'Not enough stock available to change order status to pending');
                    }
                }
            }

            // Update the order status
            $order->status = $newStatus;
            $order->save();

            return redirect()->back()->with('success', 'Order Status updated successfully');
        }

        return redirect()->back()->with('info', 'Order status remains unchanged');
    }

    // ------------------------------------------------------------Review Management
    public function storeReview(Request $request)
    {
        $review = new Review();
        $review->product_id = $request->input('product_id');
        $review->comment = $request->input('comment');
        $review->rating = $request->input('rating');
        $review->user_id = auth()->user()->id;
        $review->save();
        return redirect()->back()->with('success', 'Review submitted successfully');
    }
    public function updateReview(Request $request)
    {
        // Find the review associated with the user
        $review = Review::where('user_id', Auth::user()->id)->first();

        // Update the review
        $review->comment = $request->input('comment');
        $review->rating = $request->input('rating');
        $review->update();

        return redirect()->back()->with('success', 'Review updated successfully');
    }
    public function deleteReview()
    {
        $review = Review::where('user_id', Auth::user()->id)->first();
        $review->delete();
        return redirect()->back()->with('success', 'Review deleted successfully');
    }
    // ------------------------------------------------------------ Cart Management
    public function addToCart(Request $request)
    {
        $product = Products::findOrFail($request->input('product_id'));

        if ($product->stock > 0) {
            Cart::add($product->id, $product->name, 1, $product->price, ['description' => $product->description]);
            return redirect()->back()->with('success', 'Product added to cart');
        } else {
            return redirect()->back()->with('error', 'Product is out of stock');
        }
    }
    public function removeFromCart($rowId)
    {
        $item = Cart::get($rowId);

        if ($item) {
            $product = Products::find($item->id);
            if ($product) {
                $product->stock += $item->qty;
                $product->save();
            }

            Cart::remove($rowId);
            return redirect()->back()->with('success', 'Product removed from cart');
        }

        return redirect()->back()->with('error', 'Product not found in cart');
    }
}
