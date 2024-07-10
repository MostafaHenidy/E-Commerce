<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Products;
use App\Models\Review;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function indexProducts()
    {
        $products = Products::all();
        return view('user.products.index', compact('products'));
    }
    public function showProduct($id)
    {
        $product = Products::find($id);
        return view('user.products.show', compact('product'));
    }
    public function createOrder()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('user.products.index')->with('info', 'Your cart is empty.');
        }

        return view('user.orders.create', compact('cart'));
    }
    public function storeOrder(Request $request)
    {
        $order = new Order();
        $order->user_id = auth()->user()->id;
        $order->total_amount = array_reduce(session()->get('cart', []), function ($carry, $item) {
            return $carry + $item['price'] * $item['quantity'];
        }, 0);
        $order->status = 'pending';
        $order->save();

        foreach (session()->get('cart', []) as $item) {
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);
        }

        session()->forget('cart');

        return redirect()->route('user.orders.index')->with('success', 'Order placed successfully');
    }
    public function indexOrders()
    {
        $orders = Order::where('user_id', auth()->user()->id)->get();
        return view('user.orders.index', compact('orders'));
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
        $product_id = $request->product_id;
    $cart = session()->get('cart', []);

    if (isset($cart[$product_id])) {
        $cart[$product_id]['quantity']++;
    } else {
        $product = Products::find($product_id);
        $cart[$product_id] = [
            'product_id' => $product_id,
            'quantity' => 1,
            'price' => $product->price
        ];
    }

    session()->put('cart', $cart);
    session()->put('cart_count', count($cart));

    return response()->json(['success' => true, 'cart_count' => count($cart)]);
    }
    public function removeFromCart($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        // If the cart is empty, clear the session
        if (empty($cart)) {
            session()->forget('cart');
        }

        return redirect()->route('user.orders.create')->with('success', 'Item removed from cart.');
    }
}
