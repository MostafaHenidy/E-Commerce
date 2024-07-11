<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorController extends Controller
{
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
        $product = new Products($request->all());
        $product->vendor_id = auth('vendor')->user()->id;
        $product->save();
        return redirect()->route('vendor.products.index')->with('success', 'Product created successfully');
    }
    public function updateProduct(Request $request, $id)
    {
        $product = Products::find($id);
        $product->update($request->all());
        return redirect()->route('vendor.products.index')->with('success', 'Product updated successfully');
    }
    public function deleteProduct($id)
    {
        Products::find($id)->delete();
        return redirect()->back()->with('success', 'Product deleted successfully');
    }
    // public function indexOrders(){
    //     $products = Products::where('vendor_id',Auth::user()->id)->get();
    //     $orderItems = OrderItem::where('product_id',$products->id);
    //     $order = Order::where
    //     return view('vendor.orders.index',compact('orders'));
    // }
}
