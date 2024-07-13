<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Order;
use App\Models\Products;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AdminController extends Controller
{
    // -----------------------User management-----------------
    public function indexUsers()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }
    public function deleteUser($id)
    {
        User::find($id)->delete();
        return redirect()->back()->with('success', 'User deleted successfully');
    }
    // -----------------------Product management-----------------
    public function indexProducts()
    {
        $products = Products::with('category')->get();
        return view('admin.products.index', compact('products'));
    }
    public function createProduct()
    {
        $categories = Categories::all();
        return view('admin.products.create', compact('categories'));
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
        $product->vendor_id = auth('admin')->user()->id;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('products', $imageName, 'public');

            $product->image = '/storage/' . $imagePath;
        }
        $product->save();
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully');
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

    // -----------------------Category management-----------------
    public function indexCategories()
    {
        $categories = Categories::all();
        return view('admin.categories.index', compact('categories'));
    }
    public function createCategories()
    {
        $categories = Categories::all();
        return view('admin.categories.create');
    }
    public function storeCategories(Request $request)
    {
        $category = new Categories($request->all());
        $category->save();
        return redirect()->route('admin.categories.index')->with('success', 'Category created successfully');
    }
    public function updateCategories(Request $request,$id)
    {
        $category = Categories::find($id);
        $category->name =$request->name ; 
        $category->update();
        return redirect()->back()->with('success','Category updated successfully');
    }
    public function deleteCategories($id)
    {
        Categories::find($id)->delete();
        return redirect()->back()->with('success', 'Product deleted successfully');
    }
    // -----------------------Vendor management-----------------
    public function indexVendors()
    {
        $vendors = Vendor::all();
        return view('admin.vendors.index', compact('vendors'));
    }
    public function deleteVendors($id)
    {
        $vendor = Vendor::findOrFail($id);
        $vendor->delete();
        return view('admin.vendors.index', compact('vendors'));
    }
    // -----------------------Order management-----------------
    public function indexOrders()
    {
        $orders = Order::get();
        return view('admin.orders.index', compact('orders'));
    }
    // -----------------------Roles management-----------------

}
