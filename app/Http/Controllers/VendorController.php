<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductImage;
use App\Models\Products;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    function __construct()
    {
        $this->middleware('vendor');
    }
    // ------------------------------------Products Management
    public function indexProducts()
    {
        $products = Products::with('category')->get();
        $categories = Categories::all();
        return view('vendor.products.index', compact('products', 'categories'));
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
    public function showProduct($id)
    {
        $product = Products::with('category')->findOrFail($id);
        $reviews = Review::where('product_id', $id)->get();
        $averageRating = $reviews->avg('rating');
        $productImages = ProductImage::where('product_id', $id)->get();
        return view('vendor.products.show', compact('product', 'reviews', 'averageRating', 'productImages'));
    }
    public function uploadMultipleImages(Request $request, $id)
    {
        $product = Products::findOrFail($id);

        $request->validate([
            'images.*' => 'nullable|image',
        ]);

        $files = $request->file('images');
        if ($files) {
            foreach ($files as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('productImages', $fileName, 'public');
                $imagePath = '/storage/' . $filePath;

                $image = new ProductImage();
                $image->image = $imagePath;
                $image->product_id = $product->id;
                $image->save();
            }
        }

        return redirect()->back()->with('success', 'Images uploaded successfully');
    }
    public function deleteMultipleImages(Request $request, $id)
    {
        $product = Products::findOrFail($id);
        $productImages = ProductImage::where('product_id', $id)->get();
        foreach ($productImages as $image) {
            if (Storage::exists('public' . $image->image)) {
                Storage::delete('public' . $image->image);
            }
            $image->delete();
        }

        return redirect()->back()->with('success', 'Product images deleted');
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


    public function setDiscount(Request $request)
    {
        $request->validate([
            'discount' => 'required|numeric|min:0|max:100',
            'discount_start_date' => 'required|date',
            'discount_end_date' => 'required|date|after_or_equal:discount_start_date',
        ]);

        $vendorId = Auth::guard('vendor')->user()->id;
        $products = Products::where('vendor_id', $vendorId)->get();

        $discountStartDate = $request->discount_start_date;
        $discountEndDate = $request->discount_end_date;

        foreach ($products as $product) {
            $product->update([
                'discount' => $request->discount,
                'discount_start_date' => $discountStartDate,
                'discount_end_date' => $discountEndDate,
            ]);
        }

        return redirect()->route('vendor.products.index')->with('success', 'Discount applied to all your products successfully.');
    }


    // ------------------------------------Orders Management
    public function indexOrders()
    {
        $vendorId = auth('vendor')->id();
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
