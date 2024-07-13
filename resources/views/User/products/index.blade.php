@extends('user.master')
@section('title', 'Products')
@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="d-flex flex-row">
            <h4 class="fw-bold py-3 mb-4">Products Table</h4>
            <div class="ms-auto">
                <a href="{{ route('user.orders.create') }}" class="btn btn-primary float-end">
                    <i class="bx bx-cart"></i>
                    <span id="cart-count">Cart ({{ Cart::count() }})</span>
                </a>
            </div>
        </div>
        <div class="mb-4 d-flex flex-row">
            <p class="fw-bold mt-2 me-3 d-flex">Filter By:</p>
            <form action="{{ route('user.products.index') }}" method="GET" class="d-flex">
                <div class="me-3">
                    <select name="category_id" class="form-select ">
                        <option class="btn btn-primary" value="">Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="me-3">
                    <input type="number" name="min_price" class="form-control" placeholder="Min Price"
                        value="{{ request('min_price') }}">
                </div>
                <div class="me-3">
                    <input type="number" name="max_price" class="form-control" placeholder="Max Price"
                        value="{{ request('max_price') }}">
                </div>
                <div>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>
        @if ($products->isEmpty())
            <p>No products found</p>
        @else
            <div class="row mb-5">
                @foreach ($products as $product)
                    <div class="col-md-6 col-lg-4 mb-3">
                        <div class="card h-70">
                            <div class="card-body">
                                <h5 class="card-title">{{ $product->name }}</h5>
                                <img class="img-fluid d-flex mx-auto my-4" style="max-width: 200px; max-height: 200px;"
                                    src="{{ $product->image }}" alt="Card image cap" />
                                <p class="card-text">Price: {{ $product->price }}$</p>
                                <div class="my-2">
                                    @if ($product->stock <= 0)
                                        <span class="badge bg-danger">Out of stock</span>
                                    @else
                                        <p class="card-text">Stock: {{ $product->stock }}</p>
                                    @endif
                                </div>
                                <a href="{{ route('user.products.show', $product->id) }}" class="card-link">More
                                    Details</a>
                                <form class="card-link" action="{{ route('user.cart.add') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <button type="submit" class="btn btn-primary"
                                        @if ($product->stock <= 0) disabled @endif>
                                        <i class="bx bx-cart pb-1"></i>
                                        @if ($product->stock <= 0)
                                            Out of Stock
                                        @else
                                            Add to cart
                                        @endif
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
