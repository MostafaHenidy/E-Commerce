<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Message;
use App\Models\Order;
use App\Models\ProductImage;
use App\Models\Products;
use App\Models\Review;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    function __construct()
    {
        $this->middleware('admin');
    }
    // -----------------------User management-----------------
    public function indexUsers()
    {
        $users = User::all();
        $permissions = Permission::where('guard_name', 'user')->get();
        return view('admin.users.index', compact('users', 'permissions'));
    }
    public function deleteUser($id)
    {
        User::find($id)->delete();
        return redirect()->back()->with('success', 'User deleted successfully');
    }
    // -----------------------Product management-----------------
    public function indexProducts()
    {
        $products = Products::all();
        $categories = Categories::all();
        return view('admin.products.index', compact('products', 'categories'));
    }
    public function showProduct($id)
    {
        $product = Products::find($id);
        $reviews = Review::where('product_id', $id)->with('user')->get();
        $averageRating = $reviews->avg('rating');
        $productImages = ProductImage::where('product_id', $id)->get();
        return view('admin.products.show', compact('product', 'reviews', 'averageRating', 'productImages'));
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
    public function updateCategories(Request $request, $id)
    {
        $category = Categories::find($id);
        $category->name = $request->name;
        $category->update();
        return redirect()->back()->with('success', 'Category updated successfully');
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
    public function viewOrder($id)
    {
        $order = Order::with('orderItems.product')->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }
    // -----------------------Roles management-----------------
    public function indexRoles()
    {
        $roles = Role::all();
        return view('admin.roles.index', compact('roles'));
    }
    public function support()
    {
        $messages = Message::where('receiver_id',Auth::guard('admin')->user()->id);
        return view('admin.support.index',compact('messages'));
    }
}
