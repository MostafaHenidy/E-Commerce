<?php

namespace App\Http\Controllers;

use App\Events\UserPlacedOrderEvent;
use App\Models\Categories;
use App\Models\Message;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductImage;
use App\Models\Products;
use App\Models\Review;
use App\Models\Vendor;
use App\Models\Wishlist;
use App\Models\WishlistItem;
use App\Notifications\UserPlacedOrderNotification;
use Carbon\Carbon;
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
        // $query = Products::query();

        // // Filter by category
        // if ($request->has('category_id') && $request->category_id != '') {
        //     $query->where('category_id', $request->category_id);
        // }

        // // Filter by price after discount
        // if (($request->has('min_price') && $request->min_price != '') || ($request->has('max_price') && $request->max_price != '')) {
        //     $query->where(function ($query) use ($request) {
        //         if ($request->has('min_price') && $request->min_price != '') {
        //             $query->whereRaw('price - (price * discount / 100) >= ?', [$request->min_price]);
        //         }
        //         if ($request->has('max_price') && $request->max_price != '') {
        //             $query->whereRaw('price - (price * discount / 100) <= ?', [$request->max_price]);
        //         }
        //     });
        // }

        // // Fetch products with vendor relationships
        // $products = $query->with('vendor')->get();

        // $categories = Categories::all();
        // $cart = Cart::content();
        // $user = Auth::user();
        // $wishlistItems = WishlistItem::whereHas('wishlist', function ($query) use ($user) {
        //     $query->where('user_id', $user->id);
        // })->with('product')->get();
        return view('user.products.index');
    }

    public function search(Request $request)
    {
        // $searchTerm = $request->input('search');
        // $products = Products::search($searchTerm)->get();
        // $categories = Categories::all();
        // $cart = Cart::content();

        // if ($products->isEmpty()) {
        //     return redirect()->route('user.products.index')->with('status', 'No products found');
        // }
        // $user = Auth::user();
        // $wishlistItems = WishlistItem::whereHas('wishlist', function ($query) use ($user) {
        //     $query->where('user_id', $user->id);
        // })->with('product')->get();

        return view('user.products.index');
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

        foreach ($cart as $item) {
            $product = Products::find($item->id);
            if ($product) {
                $originalPrice = $product->price; // Fetch the original price
                $discount = $product->discount ?? 0; // Fetch the discount percentage
                $discountEndDate = $product->discount_end_date; // Fetch the discount end date

                // Check if the discount is still valid
                if ($discount > 0 && $discountEndDate && Carbon::now()->lte(Carbon::parse($discountEndDate))) {
                    $discountedPrice = $originalPrice - ($originalPrice * ($discount / 100));
                } else {
                    $discountedPrice = $originalPrice;
                    $discount = 0; // Set discount to 0 if it's no longer valid
                }

                // Update cart item with original price and discounted price
                Cart::update($item->rowId, [
                    'price' => $originalPrice,
                    'options' => [
                        'discount' => $discount,
                        'original_price' => $originalPrice,
                        'discounted_price' => $discountedPrice
                    ]
                ]);
            }
        }

        return view('user.orders.create', compact('cart'));
    }


    public function storeOrder(Request $request)
    {
        $cartContent = Cart::content();
        $totalAmount = 0;

        // Group cart items by vendor
        $vendorOrders = [];
        foreach ($cartContent as $item) {
            $product = Products::find($item->id);
            $vendorId = $product->vendor_id;
            if (!isset($vendorOrders[$vendorId])) {
                $vendorOrders[$vendorId] = [
                    'total_amount' => 0,
                    'items' => [],
                ];
            }
            $discount = $item->options->discount;
            $discountEndDate = $product->discount_end_date; // Fetch the discount end date

            // Check if the discount is still valid
            if ($discount > 0 && $discountEndDate && Carbon::now()->lte(Carbon::parse($discountEndDate))) {
                $discountedPrice = $item->options->discounted_price;
            } else {
                $discountedPrice = $item->price;
            }

            $vendorOrders[$vendorId]['total_amount'] += $discountedPrice * $item->qty;
            $vendorOrders[$vendorId]['items'][] = [
                'item' => $item,
                'discounted_price' => $discountedPrice,
            ];
        }

        // Create separate orders for each vendor
        foreach ($vendorOrders as $vendorId => $vendorOrder) {
            $order = new Order();
            $order->user_id = auth()->user()->id;
            $order->total_amount = $vendorOrder['total_amount'];
            $order->status = 'pending'; // Set initial status as 'pending'
            $order->save();

            $vendor = Vendor::find($vendorId);
            $vendor->notify(new UserPlacedOrderNotification(auth()->user()));
            UserPlacedOrderEvent::dispatch(auth()->user());
            // Create order items for each vendor order
            foreach ($vendorOrder['items'] as $orderItemData) {
                $item = $orderItemData['item'];
                $discountedPrice = $orderItemData['discounted_price'];

                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->product_id = $item->id;
                $orderItem->quantity = $item->qty;
                $orderItem->price = $discountedPrice;
                $orderItem->save();

                $product = Products::find($item->id);
                if ($product) {
                    $product->stock -= $item->qty;
                    $product->save();
                }
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


    public function support()
    {
        return view('user.support.index');
    }
}
