<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Products;
use App\Models\Review;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    // ------------------------------------------------------------Product Management
    public function indexProducts()
    {
        $products = Products::all();
        $cart = Cart::content();
        return view('user.products.index', compact('products', 'cart'));
    }
    public function showProduct($id)
    {
        $product = Products::find($id);
        $reviews = Review::where('product_id', $id)->with('user')->get();
        return view('user.products.show', compact('product', 'reviews'));
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
