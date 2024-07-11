<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Products;
use App\Models\Review;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function indexProducts()
    {
        $products = Products::all();
        $cart = Cart::content();
        return view('user.products.index', compact('products', 'cart'));
    }
    public function showProduct($id)
    {
        $product = Products::find($id);
        return view('user.products.show', compact('product'));
    }
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
        $order = Order::with('orderItems.products')->findOrFail($id);  // Eager load order items
        return view('user.orders.show', compact('order'));
    }
    public function storeReview(Request $request)
    {
        $review = new Review($request->all());
        $review->user_id = auth()->user()->id;
        $review->save();
        return redirect()->back()->with('success', 'Review submitted successfully');
    }
    public function addToCart(Request $request)
    {
        $product = Products::findOrFail($request->input('product_id'));
        if ($product->stock > 0) {
            $product->stock -= 1;
            $product->save();
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
