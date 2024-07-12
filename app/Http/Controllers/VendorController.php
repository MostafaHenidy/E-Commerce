<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
    // ------------------------------------Products Management
    public function indexProducts()
    {
        $products = Products::where('vendor_id', auth('vendor')->user()->id)->get();
        return view('vendor.products.index', compact('products'));
    }
    public function createProduct()
    {
        return view('vendor.products.create');
    }
    public function storeProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate image
        ]);

        $product = new Products($request->except('image'));
        $product->vendor_id = auth('vendor')->user()->id;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('products', $imageName, 'public');

            $product->image = '/storage/' . $imagePath;
        }

        $product->save();

        return redirect()->route('vendor.products.index')->with('success', 'Product created successfully');
    }
    public function updateProduct(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validate image
        ]);
        $product = Products::find($id);
        $product = new Products($request->except('image'));
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('products', $imageName, 'public');
            $product->image = '/storage/' . $imagePath;
        }
        $product->update();
        return redirect()->route('vendor.products.index')->with('success', 'Product updated successfully');
    }
    public function deleteProduct($id)
    {
        Products::find($id)->delete();
        return redirect()->back()->with('success', 'Product deleted successfully');
    }
    // ------------------------------------Orders Management
    public function indexOrders()
    {
        $vendorId = auth('vendor')->id(); // Assuming vendor is the logged-in user
        $orders = Order::whereHas('orderItems.product', function ($query) use ($vendorId) {
            $query->where('vendor_id', $vendorId);
        })->with(['orderItems.product', 'user'])->get();

        return view('vendor.orders.index', compact('orders'));
    }
    public function viewOrder($id)
    {
        $order = Order::with('orderItems.product')->findOrFail($id);
        return view('vendor.orders.show', compact('order'));
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
}
