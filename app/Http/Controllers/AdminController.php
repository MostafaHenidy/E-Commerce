<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\Products;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
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
    public function indexProducts()
    {
        $products = Products::all();
        return view('admin.products.index', compact('products'));
    }
    public function createProduct()
    {
        $categories = Categories::all();
        return view('admin.products.create', compact('categories'));
    }
    public function storeProduct(Request $request)
    {
        $product = new Products($request->all());
        $product->save();
        return redirect()->route('admin.products.index')->with('success', 'Product created successfully');
    }
    public function deleteProduct($id)
    {
        Products::find($id)->delete();
        return redirect()->back()->with('success', 'Product deleted successfully');
    }

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
    public function deleteCategories($id)
    {
        Categories::find($id)->delete();
        return redirect()->back()->with('success', 'Product deleted successfully');
    }
}
