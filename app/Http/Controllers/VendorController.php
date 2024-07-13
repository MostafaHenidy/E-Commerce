<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class VendorController extends Controller
{
    // ------------------------------------Products Management
    public function indexProducts()
    {
        $products = Products::where('vendor_id', auth('vendor')->user()->id)->with('category')->get();
        $categories = Categories::all();
        return view('vendor.products.index', compact('products', 'categories'));
    }
    public function createProduct()
    {
        return view('vendor.products.create');
    }
    public function storeProduct(Request $request)
    {
        $product = new Products();
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category_id' => 'required|integer|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'sizes' => 'required|array',
            'colors' => 'required|array',
            'image' => 'nullable|image|max:2048',
        ]);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('products', $imageName, 'public');

            $product->image = '/storage/' . $imagePath;
        }

        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->vendor_id = Auth::guard('vendor')->user()->id;
        $product->category_id = $request->category_id;
        $product->stock = $request->stock;
        $product->sizes = implode(',', $request->sizes); // Store sizes as a comma-separated string
        $product->colors = implode(',', $request->colors); // Store colors as a comma-separated string



        $product->save();

        return redirect()->route('vendor.products.index')->with('success', 'Product created successfully.');
    }


    public function updateProduct(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category_id' => 'required|integer|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'sizes' => 'required|array',
            'colors' => 'required|array',
            'image' => 'nullable|image|max:2048',
        ]);
        $product = Products::find($id);
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('products', $imageName, 'public');
            // $image->move($imagePath, $imageName);
            $product->image = '/storage/' . $imagePath;
            if (File::exists($product->image)) {
                File::delete($product->image);
            }
        }

        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        $product->vendor_id = Auth::guard('vendor')->user()->id;
        $product->category_id = $request->category_id;
        $product->stock = $request->stock;
        $product->sizes = implode(',', $request->sizes); // Store sizes as a comma-separated string
        $product->colors = implode(',', $request->colors); // Store colors as a comma-separated string
        $product->update();

        return redirect()->route('vendor.products.index')->with('success', 'Product updated successfully');
    }
    public function deleteProduct($id)
    {
        $product = Products::find($id);
        if (File::exists($product->image)) {
            File::delete($product->image);
        }
        $product->delete();
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
